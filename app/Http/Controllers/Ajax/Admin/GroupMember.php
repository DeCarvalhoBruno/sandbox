<?php

namespace App\Http\Controllers\Ajax\Admin;

use App\Http\Controllers\Admin\Controller;
use App\Http\Requests\Admin\UpdateMember;
use App\Support\Providers\Group as GroupProvider;
use Illuminate\Http\Response;

class GroupMember extends Controller
{

    /**
     * @param string $groupName
     * @param \App\Contracts\Models\Group|\App\Support\Providers\Group $groupProvider
     * @return \Illuminate\Http\Response
     */
    public function index($groupName, GroupProvider $groupProvider)
    {
        return response($groupProvider->getMembers($groupName), Response::HTTP_OK);
    }

    /**
     * @param string $groupName
     * @param string $search
     * @param \App\Contracts\Models\Group|\App\Support\Providers\Group $groupProvider
     * @return \Illuminate\Http\Response
     */
    public function search($groupName, $search, GroupProvider $groupProvider)
    {
        return response($groupProvider->searchMembers($groupName, $search), Response::HTTP_OK);
    }

    /**
     * @param string $groupName
     * @param \App\Contracts\Models\Group|\App\Support\Providers\Group $groupProvider
     * @param \App\Http\Requests\Admin\UpdateMember $request
     * @return \Illuminate\Http\Response
     */
    public function update($groupName, GroupProvider $groupProvider, UpdateMember $request)
    {
        $groupProvider->updateMembers($groupName, (object)$request->input());

        return response(null, Response::HTTP_NO_CONTENT);
    }


}
