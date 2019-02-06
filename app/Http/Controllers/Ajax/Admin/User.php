<?php

namespace App\Http\Controllers\Ajax\Admin;

use App\Contracts\Models\User as UserProvider;
use App\Contracts\Models\Permission as PermissionProvider;
use App\Contracts\RawQueries;
use App\Events\PermissionEntityUpdated;
use App\Filters\User as UserFilter;
use App\Http\Controllers\Admin\Controller;
use App\Http\Requests\Admin\UpdateUser;
use App\Models\Entity;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class User extends Controller
{
    /**
     * @param \App\Contracts\Models\User|\App\Support\Providers\User $userProvider
     * @param \App\Filters\User $userFilter
     * @return array
     */
    public function index(UserProvider $userProvider, UserFilter $userFilter)
    {
        $userProvider->setStoredFilter($this->user->getKey(), $userFilter);
        $users = $userProvider
            ->select([
                \DB::raw('null as selected'),
                'full_name',
                'email',
                'created_at',
                'permission_mask',
                'username'
            ])->entityType()
            ->permissionRecord()
            ->permissionStore()
            ->permissionMask($this->user->getEntityType())
            ->activated()
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
            'columns' => $userProvider->createModel()->getColumnInfo([
                'full_name' => (object)[
                    'name' => trans('ajax.db.full_name'),
                ],
                'email' => (object)[
                    'name' => trans('ajax.general.email'),
                ],
                'created_at' => (object)[
                    'name' => trans('ajax.db.user_created_at'),
                ]
            ], $userFilter)
        ];
    }

    /**
     * @param $username
     * @param \App\Contracts\Models\User|\App\Support\Providers\User $userProvider
     * @return mixed
     */
    public function edit($username, UserProvider $userProvider)
    {
        $filter = $userProvider->getStoredFilter($this->user->getKey(), Entity::USERS);
        $nav = [];
        if (!is_null($filter)) {
            $userSiblings = $userProvider->select(
                [
                    'username',
                ])->entityType()
                ->permissionRecord()
                ->permissionStore()
                ->permissionMask($this->user->getEntityType())
                ->activated()
                ->where('username', '!=', $this->user->getAttribute('username'))
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

        $user = $userProvider->buildOneByUsername($username,
            [
                'first_name',
                'last_name',
                'email',
                'username',
                'full_name',
                'entity_type_id'
            ])->entityType()->first();
        if (is_null($user)) {
            return response(trans('error.http.500.user_not_found'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        $entityTypeId = $user['entity_type_id'];
        unset($user['entity_type_id']);
        return [
            'user' => $user->toArray(),
            'permissions' => $userProvider->getAllUserPermissions($entityTypeId),
            'nav' => $nav,
            'intended' => null
        ];
    }

    /**
     * @param $username
     * @param \App\Http\Requests\Admin\UpdateUser $request
     * @param \App\Contracts\Models\User|\App\Support\Providers\User $userProvider
     * @param \App\Contracts\Models\Permission|\App\Support\Providers\Permission $permissionProvider
     * @return \Illuminate\Http\Response
     */
    public function update(
        $username,
        UpdateUser $request,
        UserProvider $userProvider,
        PermissionProvider $permissionProvider
    ) {
        $user = $userProvider->updateOneByUsername($username, $request->all());
        $permissions = $request->getPermissions();

        if (!is_null($permissions)) {
            $permissionProvider->updateIndividual($permissions, $user->getEntityType(), Entity::USERS);
            event(new PermissionEntityUpdated);
        }
        return response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @param string $search
     * @param \App\Contracts\Models\User|\App\Support\Providers\User $userProvider
     * @return \Illuminate\Http\Response
     */
    public function search($search, $limit, UserProvider $userProvider)
    {
        return response(
            $userProvider->search(
                preg_replace('/[^\w\s\-\']/', '', strip_tags($search)),
                $this->user->getAttribute('entity_type_id'),
                intval($limit)
            )->get(), Response::HTTP_OK);
    }

    /**
     * @param string $username
     * @param \App\Contracts\Models\User|\App\Support\Providers\User $userProvider
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy($username, UserProvider $userProvider)
    {
        $userProvider->deleteByUsername($username);
        return response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Contracts\Models\User|\App\Support\Providers\User $userProvider
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function batchDestroy(Request $request, UserProvider $userProvider)
    {
        $userProvider->deleteByUsername($request->only('users'));
        return response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @param \App\Contracts\Models\User $userProvider|\App\Support\Providers\User $userProvider
     * @return array
     */
    public function profile(UserProvider $userProvider)
    {
        $f = app()->make(RawQueries::class);
        return [
            'user' => $this->user->only([
                'first_name',
                'last_name',
                'email',
                'username',
                'full_name',
            ]),
            'avatars' => $userProvider->getAvatars($this->user->getKey())
        ];
    }


}
