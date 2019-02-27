<?php

namespace App\Http\Controllers\Ajax\Admin;

use App\Events\PermissionEntityUpdated;
use App\Http\Controllers\Admin\Controller;
use App\Filters\Group as GroupFilter;
use App\Contracts\Models\Group as GroupProvider;
use App\Http\Requests\Admin\CreateGroup;
use App\Http\Requests\Admin\UpdateGroup;
use App\Models\Entity;
use Illuminate\Http\Response;
use App\Contracts\Models\Permission as PermissionProvider;

class Group extends Controller
{
    /**
     * @param \App\Filters\Group $filter
     * @return array
     */
    public function index(GroupFilter $filter, GroupProvider $groupProvider)
    {
        return [
            'table' => $groupProvider
                ->select([
                    'groups.group_id',
                    'group_name',
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
            'columns' => (new \App\Models\Group)->getColumnInfo([
                'group_name' => (object)[
                    'name' => trans('js-backend.db.group_name'),
                    'width' => '80%'
                ]
            ],$filter),
            'member_count' => trans('js-backend.db.member_count'),

        ];

    }

    /**
     * @param string $groupName
     * @param \App\Contracts\Models\Group|\App\Support\Providers\\Group $groupProvider
     * @param \App\Contracts\Models\Permission|\App\Support\Providers\\Permission $permissionProvider
     * @return array
     * @throws \ReflectionException
     */
    public function edit($groupName, GroupProvider $groupProvider, PermissionProvider $permissionProvider)
    {
        $group = $groupProvider->getOneByName($groupName, ['group_name', 'group_mask', 'entity_type_id'])
            ->entityType()->first()->toArray();
        $entityType_id = $group['entity_type_id'];
        unset($group['entity_type_id']);

        return [
            'group' => $group,
            'permissions' => $permissionProvider->getRootAndGroupPermissions($entityType_id)
        ];
    }

    /**
     * @param string $groupName
     * @param \App\Http\Requests\Admin\UpdateGroup $request
     * @param \App\Contracts\Models\Group|\App\Support\Providers\\Group $groupProvider
     * @param \App\Contracts\Models\Permission|\App\Support\Providers\\Permission $permissionProvider
     * @return \Illuminate\Http\Response
     */
    public function update(
        $groupName,
        UpdateGroup $request,
        GroupProvider $groupProvider,
        PermissionProvider $permissionProvider
    ) {
        $group = $groupProvider->updateOneByName($groupName, $request->all());
        $permissions = $request->getPermissions();

        if (!is_null($permissions)) {
            $permissionProvider->updateIndividual(
                $permissions,
                $group->getAttribute('entity_type_id'),
                Entity::GROUPS);
            event(new PermissionEntityUpdated);
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @param string $groupName
     * @param \App\Contracts\Models\Group|\App\Support\Providers\\Group $groupProvider
     * @return \Illuminate\Http\Response
     */
    public function destroy($groupName, GroupProvider $groupProvider)
    {
        $groupProvider->deleteByName($groupName);
        return response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @param \App\Contracts\Models\Permission|\App\Support\Providers\\Permission $permissionProvider
     * @return array
     * @throws \ReflectionException
     */
    public function add(PermissionProvider $permissionProvider)
    {
        return [
            'permissions' => $permissionProvider->getRootAndGroupPermissions()
        ];
    }

    /**
     * @param \App\Http\Requests\Admin\CreateGroup $request
     * @param \App\Contracts\Models\Group|\App\Support\Providers\\Group $groupProvider
     * @param \App\Contracts\Models\Permission|\App\Support\Providers\\Permission $permissionProvider
     * @return \Illuminate\Http\Response
     */
    public function create(CreateGroup $request, GroupProvider $groupProvider, PermissionProvider $permissionProvider)
    {

        $group = $groupProvider->createOne($request->all());
        $permissions = $request->getPermissions();

        if (!is_null($permissions)) {
            $permissionProvider->updateIndividual(
                $permissions,
                $group->getAttribute('entity_type_id'), Entity::GROUPS);
        }
        event(new PermissionEntityUpdated);
        return response(null, Response::HTTP_NO_CONTENT);
    }
}
