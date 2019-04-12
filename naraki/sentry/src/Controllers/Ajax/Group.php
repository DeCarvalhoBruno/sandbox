<?php namespace Naraki\Sentry\Controllers\Ajax;

use Naraki\Sentry\Contracts\Group as GroupProvider;
use Naraki\Permission\Events\PermissionEntityUpdated;
use Naraki\Sentry\Models\Filters\Group as GroupFilter;
use Naraki\Core\Controllers\Admin\Controller;
use Naraki\Sentry\Requests\Admin\CreateGroup;
use Naraki\Sentry\Requests\Admin\UpdateGroup;
use Naraki\Core\Models\Entity;
use Illuminate\Http\Response;
use Naraki\Permission\Facades\Permission;

class Group extends Controller
{
    /**
     * @param \Naraki\Sentry\Models\Filters\Group $filter
     * @param \Naraki\Sentry\Contracts\Group|\Naraki\Sentry\Providers\Group $groupProvider
     * @return array
     */
    public function index(GroupFilter $filter, GroupProvider $groupProvider)
    {
        return [
            'table' => $groupProvider
                ->select([
                    'groups.group_id',
                    'group_name',
                    'group_slug',
                    'group_mask',
                    'permission_mask',
                    \DB::raw('count(group_members.user_id) as member_count')
                ])->leftGroupMember()->entityType()
                ->permissionRecord()
                ->permissionStore()
                ->permissionMask($this->user->getAttribute('entity_type_id'))->groupBy([
                    'groups.group_id',
                    'group_name',
                    'permission_mask',
                ])
                ->filter($filter)->paginate(10),
            'columns' => (new \Naraki\Sentry\Models\Group)->getColumnInfo([
                'group_name' => (object)[
                    'name' => trans('js-backend.db.group_name'),
                    'width' => '60%'
                ],
                'group_mask' => (object)[
                    'name' => trans('js-backend.db.group_mask'),
                    'width' => '20%'
                ]
            ], $filter),
            'member_count' => trans('js-backend.db.member_count'),

        ];

    }

    /**
     * @param string $groupSlug
     * @param \Naraki\Sentry\Contracts\Group|\Naraki\Sentry\Providers\Group $groupProvider
     * @return array
     */
    public function edit($groupSlug, GroupProvider $groupProvider)
    {
        $groupDb = $groupProvider->getOneBySlug($groupSlug,
            ['group_slug', 'group_name', 'group_mask', 'entity_type_id'])
            ->scopes(['entityType'])->first();
        if (is_null($groupDb)) {
            return response(trans('error.http.500.user_not_found'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        $group = $groupDb->toArray();
        $entityType_id = $group['entity_type_id'];
        unset($group['entity_type_id']);
        return [
            'group' => $group,
            'permissions' => Permission::getRootAndGroupPermissions($entityType_id)
        ];
    }

    /**
     * @param string $groupName
     * @param \Naraki\Sentry\Requests\Admin\UpdateGroup $request
     * @param \Naraki\Sentry\Contracts\Group|\Naraki\Sentry\Providers\Group $groupProvider
     * @return \Illuminate\Http\Response
     */
    public function update(
        $groupName,
        UpdateGroup $request,
        GroupProvider $groupProvider
    ) {
        $group = $groupProvider->updateOneBySlug($groupName, $request->all());
        $permissions = $request->getPermissions();

        if (!is_null($permissions)) {
            Permission::updateIndividual(
                $permissions,
                $group->getAttribute('entity_type_id'),
                Entity::GROUPS);
            event(new PermissionEntityUpdated);
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @param string $groupName
     * @param \Naraki\Sentry\Contracts\Group|\Naraki\Sentry\Providers\Group $groupProvider
     * @return \Illuminate\Http\Response
     */
    public function destroy($groupName, GroupProvider $groupProvider)
    {
        $groupProvider->deleteBySlug($groupName);
        return response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @param \Naraki\Permission\Contracts\Permission|\Naraki\Core\Support\Providers\\Permission $permissionProvider
     * @return array
     */
    public function add()
    {
        return [
            'permissions' => Permission::getRootAndGroupPermissions()
        ];
    }

    /**
     * @param \Naraki\Sentry\Requests\Admin\CreateGroup $request
     * @param \Naraki\Sentry\Contracts\Group|\Naraki\Sentry\Providers\Group $groupProvider
     * @return \Illuminate\Http\Response
     */
    public function create(CreateGroup $request, GroupProvider $groupProvider)
    {

        $group = $groupProvider->createOne($request->all());
        $permissions = $request->getPermissions();

        if (!is_null($permissions)) {
            Permission::updateIndividual(
                $permissions,
                $group->getAttribute('entity_type_id'),
                Entity::GROUPS);
            event(new PermissionEntityUpdated);
        }
        return response(null, Response::HTTP_NO_CONTENT);
    }
}
