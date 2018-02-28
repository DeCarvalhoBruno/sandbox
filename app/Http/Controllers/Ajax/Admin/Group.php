<?php

namespace App\Http\Controllers\Ajax\Admin;

use App\Http\Controllers\Controller;
use App\Filters\Group as GroupFilter;
use App\Providers\Models\Group as GroupProvider;
use App\Http\Requests\Admin\UpdateGroup;
use Illuminate\Http\Response;

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
                    'group_name',
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
                'group_name' => trans('ajax.db.group_name'),
                'member_count' => trans('ajax.db.member_count'),
            ])
        ];

    }

    /**
     * @param string $groupName
     * @param \App\Providers\Models\Group|\App\Providers\Models\Group $groupProvider
     * @return array
     */
    public function edit($groupName, GroupProvider $groupProvider)
    {
        return $groupProvider->getOneByName($groupName,['group_name','group_mask'])->first();
    }

    /**
     * @param string $groupName
     * @param \App\Http\Requests\Admin\UpdateGroup $request
     * @param \App\Contracts\Models\Group|\App\Providers\Models\Group $groupProvider
     * @return \Illuminate\Http\Response
     */
    public function update($groupName, UpdateGroup $request, GroupProvider $groupProvider)
    {
        $groupProvider->updateOneByName($groupName, $request->all());
        return response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @param string $groupName
     * @param \App\Contracts\Models\Group|\App\Providers\Models\Group $groupProvider
     * @return \Illuminate\Http\Response
     */
    public function members($groupName,GroupProvider $groupProvider)
    {
        return response($groupProvider->getMembers($groupName)->get(), Response::HTTP_OK);
    }


}
