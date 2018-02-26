<?php

namespace App\Http\Controllers\Ajax\Admin;

use App\Http\Controllers\Controller;
use App\Filters\Group as GroupFilter;

class Group extends Controller
{
    public function index(GroupFilter $filter)
    {
        return [
            'table' => \App\Models\Group::query()
                ->select([
                    'groups.group_id',
                    'group_name',
                    'permission_mask',
                ])->entityType()
                ->permissionRecord()
                ->permissionStore()
                ->permissionMask(auth()->user()->getAttribute('entity_type_id'))
                ->filter($filter)->paginate(10),
            'columns' => (new \App\Models\Group)->getColumnInfo([
                'group_name' => trans('ajax.db.group_name'),
            ])
        ];

    }

    public function edit()
    {
    }

    public function update()
    {
    }


}
