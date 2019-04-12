<?php namespace Naraki\Sentry\Controllers\Ajax;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Naraki\Core\Controllers\Admin\Controller;
use Naraki\Core\Models\Entity;
use Naraki\Media\Facades\Media as MediaProvider;
use Naraki\Permission\Events\PermissionEntityUpdated;
use Naraki\Permission\Facades\Permission;
use Naraki\Sentry\Contracts\Group as GroupProvider;
use Naraki\Sentry\Events\UserRegistered;
use Naraki\Sentry\Models\Filters\User as UserFilter;
use Naraki\Sentry\Requests\Admin\UpdateUser;

class User extends Controller
{
    /**
     * @var \Naraki\Sentry\Contracts\User|\Naraki\Sentry\Providers\User $userProvider
     */
    private $repo;

    public function __construct()
    {
        parent::__construct();
        $this->repo = app(\Naraki\Sentry\Contracts\User::class);
    }

    /**
     * @param \Naraki\Sentry\Models\Filters\User $userFilter
     * @return array
     */
    public function index(UserFilter $userFilter)
    {
        $this->repo->setStoredFilter(Entity::USERS, $this->user->getKey(), $userFilter);
        $usersDb = $this->repo
            ->select([
                \DB::raw('null as selected'),
                'username',
                'full_name',
                'email',
                'created_at as created_ago',
                'created_at'
            ])
            ->where('username', '!=', $this->user->getAttribute('username'))
            ->filter($userFilter);

        if (!$userFilter->hasFilters()) {
            $usersDb->orderBy('users.user_id', 'desc');
        }

        $groups = (clone $usersDb)->select('group_name')
            ->where('groups.group_id', '>', 1)
            ->groupBy('group_name');
        if (!$userFilter->hasFilter('group')) {
            $groups->groupMember();
        }
        $usersPaginated = $usersDb->paginate(25);
        unset($usersDb);

        $userPermissions = $this->repo
            ->buildWithScopes([
                'username',
                'permission_mask as acl'
            ], [
                'entityType',
                'permissionRecord',
                'permissionStore',
                'permissionMask' => $this->user->getEntityType(),
            ])
            ->whereIn('username', $usersPaginated->pluck('username')->all())
            ->orderBy('users.user_id', 'asc')
            ->get();
        $permissions = [];
        foreach ($userPermissions as $permission) {
            $permissions[$permission->getAttribute('username')] = $permission->getAttribute('acl');
        }
        foreach ($usersPaginated as $user) {
            //if the currently logged in user does not have permissions on this particular user,
            //the acl value will be 0. We need to distinguish that case from users not having permissions because
            //no permissions are assigned or they're not part of groups
            //So when we don't have permissions, we set the bitmask the 16 so that all binary comparisons fail
            //If no permissions exist, we set the bitmask to 0.
            $user->setAttribute('acl',
                isset($permissions[$user->getAttribute('username')])
                    ? ($permissions[$user->getAttribute('username')] > 0)
                    ? $permissions[$user->getAttribute('username')]
                    : 16
                    : 0
            );
        }

        return [
            'table' => $usersPaginated,
            'groups' => $groups->pluck('group_name'),
            'sorted' => $userFilter->getFilter('sortBy'),
            'columns' => $this->repo->createModel()->getColumnInfo([
                'full_name' => (object)[
                    'name' => trans('js-backend.db.full_name'),
                ],
                'email' => (object)[
                    'name' => trans('js-backend.general.email'),
                ],
                'created_ago' => (object)[
                    'name' => trans('js-backend.db.user_created_at'),
                ]
            ], $userFilter)
        ];
    }

    /**
     * @param \Naraki\Sentry\Contracts\Group|\Naraki\Sentry\Providers\Group $groupProvider
     * @return array
     */
    public function add(GroupProvider $groupProvider)
    {
        return [
            'user' => [],
            'nav' => [],
            'intended' => null,
            'groups' => $groupProvider
                ->buildWithScopes([
                    'group_name as name',
                    'group_slug as slug',
                    \DB::raw('false as isMember'),
                ], [
                    'leftGroupMember',
                    'entityType',
                    'permissionRecord',
                    'permissionStore',
                    'permissionMask' => $this->user->getAttribute('entity_type_id')
                ])
                ->groupBy([
                    'group_slug',
                ])->get(),
            'media' => []
        ];
    }

    /**
     * @param \Naraki\Sentry\Requests\Admin\UpdateUser $request
     * @param \Naraki\Sentry\Contracts\Group|\Naraki\Sentry\Providers\Group $groupProvider
     * @return \Illuminate\Http\Response
     */
    public function create(UpdateUser $request, GroupProvider $groupProvider)
    {
        $user = $this->repo->createOne($request->all(), true);

        if ($request->hasGroups()) {
            $groupProvider->updateSingleMemberGroups($user->getKey(), $request->getGroups());
        }

        if ($request->hasPermissions()) {
            Permission::updateIndividual($request->getPermissions(), $user->getEntityType(), Entity::USERS);
            Permission::cacheUserPermissions($user->getEntityType());
        }

        if ($request->hasPermissions() || $request->hasGroups()) {
            event(new PermissionEntityUpdated);
        }
        event(new UserRegistered($user));

        return response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @param $username
     * @param \Naraki\Sentry\Contracts\Group|\Naraki\Sentry\Providers\Group $groupProvider
     * @return mixed
     */
    public function edit($username, GroupProvider $groupProvider)
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
        $media = MediaProvider::image()
            ->buildImages($entityTypeId, null, true)
            ->first();

        $allVisibleGroups = $groupProvider
            ->buildWithScopes([
                'group_name as name',
                'group_slug as slug',
            ], [
                'leftGroupMember',
                'entityType',
                'permissionRecord',
                'permissionStore',
                'permissionMask' => $this->user->getAttribute('entity_type_id')
            ])
            ->groupBy([
                'group_slug',
            ])->get();
        $userGroupsDb = $this->repo->buildOneWithScopes([
            'groups.group_id as id',
            'group_slug as slug'
        ], ['groupMember'], [['username', $username]])->pluck('id', 'slug');
        $userGroups = $userGroupsDb->toArray();
        foreach ($allVisibleGroups as $grp) {
            if (isset($userGroups[$grp->getAttribute('slug')])) {
                $grp->setAttribute('isMember', true);
            } else {
                $grp->setAttribute('isMember', false);
            }
        }

        unset($user['entity_type_id']);
        return [
            'user' => $user->toArray(),
            'permissions' => Permission::getAllUserPermissions($entityTypeId),
            'groups' => $allVisibleGroups->toArray(),
            'nav' => $nav,
            'intended' => null,
            'media' => $media
        ];
    }

    /**
     * @param $username
     * @param \Naraki\Sentry\Requests\Admin\UpdateUser $request
     * @param \Naraki\Sentry\Contracts\Group|\Naraki\Sentry\Providers\Group $groupProvider
     * @return \Illuminate\Http\Response
     */
    public function update(
        $username,
        UpdateUser $request,
        GroupProvider $groupProvider
    ) {
        $user = $this->repo->updateOneByUsername($username, $request->all());

        if ($request->hasGroups()) {
            $groupProvider->updateSingleMemberGroups($user->getKey(), $request->getGroups());
        }

        if ($request->hasPermissions()) {
            Permission::updateIndividual($request->getPermissions(), $user->getEntityType(), Entity::USERS);
            Permission::cacheUserPermissions($user->getEntityType());
        }

        if ($request->hasPermissions() || $request->hasGroups()) {
            event(new PermissionEntityUpdated);
        }

        event(new UserRegistered($user));

        return response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @param string $search
     * @param $limit
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
