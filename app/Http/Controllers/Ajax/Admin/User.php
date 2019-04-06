<?php namespace App\Http\Controllers\Ajax\Admin;

use App\Events\PermissionEntityUpdated;
use App\Filters\User as UserFilter;
use App\Http\Controllers\Admin\Controller;
use App\Http\Requests\Admin\UpdateUser;
use App\Models\Entity;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Naraki\Permission\Contracts\Permission as PermissionProvider;

class User extends Controller
{
    /**
     * @var \App\Contracts\Models\User|\App\Support\Providers\User $userProvider
     */
    private $repo;

    public function __construct()
    {
        parent::__construct();
        $this->repo = app(\App\Contracts\Models\User::class);
    }

    /**
     * @param \App\Filters\User $userFilter
     * @return array
     */
    public function index(UserFilter $userFilter)
    {
        $this->repo->setStoredFilter(Entity::USERS, $this->user->getKey(), $userFilter);
        $users = $this->repo
            ->buildWithScopes([
                \DB::raw('null as selected'),
                'full_name',
                'email',
                'created_at',
                'permission_mask',
                'username'
            ], [
                'entityType',
                'permissionRecord',
                'permissionStore',
                'permissionMask' => $this->user->getEntityType(),
                'activated'
            ])
            ->where('username', '!=', $this->user->getAttribute('username'))
            ->filter($userFilter);
        $groups = (clone $users)->select('group_name')->groupBy('group_name');
        if (!$userFilter->hasFilter('group')) {
            $groups->groupMember();
        }
        return [
            'table' => $users->paginate(25),
            'groups' => $groups->pluck('group_name'),
            'sorted' => $userFilter->getFilter('sortBy'),
            'columns' => $this->repo->createModel()->getColumnInfo([
                'full_name' => (object)[
                    'name' => trans('js-backend.db.full_name'),
                ],
                'email' => (object)[
                    'name' => trans('js-backend.general.email'),
                ],
                'created_at' => (object)[
                    'name' => trans('js-backend.db.user_created_at'),
                ]
            ], $userFilter)
        ];
    }

    /**
     * @param $username
     * @return mixed
     */
    public function edit($username)
    {
        $filter = $this->repo->getStoredFilter(Entity::USERS, $this->user->getKey());
        $nav = [];
        if (!is_null($filter)) {
            $userSiblings = $this->repo->buildWithScopes([
                'username',
            ], [
                'entityType',
                'permissionRecord',
                'permissionStore',
                'permissionMask' => $this->user->getEntityType()
            ])->where('username', '!=', $this->user->getAttribute('username'))
                ->filter($filter)
                ->pluck('username')->all();

            $total = count($userSiblings);
            $index = array_search($username, $userSiblings);
            $nav = array(
                'total' => $total,
                'idx' => ($index + 1),
                'first' => $userSiblings[0],
                'last' => $userSiblings[($total - 1)],
                'prev' => ($userSiblings[$index - 1] ?? null),
                'next' => ($userSiblings[$index + 1] ?? null)
            );
        }
        $user = $this->repo->buildOneByUsername($username,
            [
                'first_name',
                'last_name',
                'email',
                'username',
                'full_name',
                'entity_type_id'
            ])->scopes(['entityType'])->first();
        if (is_null($user)) {
            return response(trans('error.http.500.user_not_found'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        $entityTypeId = $user['entity_type_id'];
        unset($user['entity_type_id']);
        return [
            'user' => $user->toArray(),
            'permissions' => $this->repo->getAllUserPermissions($entityTypeId),
            'nav' => $nav,
            'intended' => null
        ];
    }

    /**
     * @param $username
     * @param \App\Http\Requests\Admin\UpdateUser $request
     * @param \Naraki\Permission\Contracts\Permission|\App\Support\Providers\Permission $permissionProvider
     * @return \Illuminate\Http\Response
     */
    public function update(
        $username,
        UpdateUser $request,
        PermissionProvider $permissionProvider
    ) {
        $user = $this->repo->updateOneByUsername($username, $request->all());
        $permissions = $request->getPermissions();

        if (!is_null($permissions)) {
            $permissionProvider->updateIndividual($permissions, $user->getEntityType(), Entity::USERS);
            event(new PermissionEntityUpdated);
        }
        return response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @param string $search
     * @return \Illuminate\Http\Response
     */
    public function search($search, $limit)
    {
        return response(
            $this->repo->search(
                preg_replace('/[^\w\s\-\']/', '', strip_tags($search)),
                $this->user->getAttribute('entity_type_id'),
                intval($limit)
            )->get(), Response::HTTP_OK);
    }

    /**
     * @param string $username
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy($username)
    {
        $this->repo->deleteByUsername($username);
        return response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function batchDestroy(Request $request)
    {
        $this->repo->deleteByUsername($request->only('users'));
        return response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function profile()
    {
        return response([
            'user' => $this->user->only([
                'first_name',
                'last_name',
                'email',
                'username',
                'full_name',
            ]),
            'avatars' => $this->repo->getAvatars($this->user->getKey())
        ], Response::HTTP_OK);
    }


}
