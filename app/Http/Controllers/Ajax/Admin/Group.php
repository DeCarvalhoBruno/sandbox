<?php namespace App\Http\Controllers\Ajax\Admin;

use App\Contracts\Models\Group as GroupProvider;
use App\Events\PermissionEntityUpdated;
use App\Filters\Group as GroupFilter;
use App\Http\Controllers\Admin\Controller;
use App\Http\Requests\Admin\CreateGroup;
use App\Http\Requests\Admin\UpdateGroup;
use App\Models\Entity;
use Illuminate\Http\Response;
use Naraki\Permission\Facades\Permission;

class Group extends Controller
{
    /**
     * @param \App\Filters\Group $filter
     * @param \App\Contracts\Models\Group $groupProvider
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
            'columns' => (new \App\Models\Group)->getColumnInfo([
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
     * @param \App\Contracts\Models\Group|\App\Support\Providers\Group $groupProvider
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
     * @param \App\Http\Requests\Admin\UpdateGroup $request
     * @param \App\Contracts\Models\Group|\App\Support\Providers\Group $groupProvider
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
     * @param \App\Contracts\Models\Group|\App\Support\Providers\Group $groupProvider
     * @return \Illuminate\Http\Response
     */
    public function destroy($groupName, GroupProvider $groupProvider)
    {
        $groupProvider->deleteByName($groupName);
        return response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @param \Naraki\Permission\Contracts\Permission|\App\Support\Providers\\Permission $permissionProvider
     * @return array
     */
    public function add()
    {
        return [
            'permissions' => Permission::getRootAndGroupPermissions()
        ];
    }

    /**
     * @param \App\Http\Requests\Admin\CreateGroup $request
     * @param \App\Contracts\Models\Group|\App\Support\Providers\Group $groupProvider
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
