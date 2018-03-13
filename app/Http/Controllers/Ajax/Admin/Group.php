<?php

namespace App\Http\Controllers\Ajax\Admin;

use App\Http\Controllers\Controller;
use App\Filters\Group as GroupFilter;
use App\Contracts\Models\Group as GroupProvider;
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
    public function index(GroupFilter $filter)
    {
        return [
            'table' => \App\Models\Group::query()
                ->select([
                    'groups.group_id',
                    'group_name as ' . trans('ajax.db_raw_inv.group_name'),
                    'permission_mask',
                    \DB::raw('count(group_members.user_id) as member_count')
                ])->groupMember()->entityType()
                ->permissionRecord()
                ->permissionStore()
                ->permissionMask(auth()->user()->getAttribute('entity_type_id'))->groupBy([
                    'groups.group_id',
                    'group_name',
                    'permission_mask',
                ])
                ->filter($filter)->paginate(10),
            'columns' => (new \App\Models\Group)->getColumnInfo([
                trans('ajax.db_raw_inv.group_name') => trans('ajax.db.group_name'),
                'member_count' => trans('ajax.db.member_count'),
            ])
        ];

    }

    /**
     * @param string $groupName
     * @param \App\Contracts\Models\Group|\App\Providers\Models\Group $groupProvider
     * @param \App\Contracts\Models\Permission|\App\Providers\Models\Permission $permissionProvider
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
     * @param \App\Contracts\Models\Group|\App\Providers\Models\Group $groupProvider
     * @param \App\Contracts\Models\Permission $permissionProvider
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
            $permissionProvider->updateIndividual($permissions, $group->getAttribute('entity_type_id'), Entity::GROUPS);
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function destroy()
    {

    }
}
