<?php

namespace App\Http\Controllers\Ajax\Admin;

use App\Events\PermissionEntityUpdated;
use App\Http\Controllers\Admin\Controller;
use App\Http\Requests\Admin\UpdateMember;
use App\Support\Providers\Group as GroupProvider;
use Illuminate\Http\Response;

class GroupMember extends Controller
{

    /**
     * @param string $slug
     * @param \App\Contracts\Models\Group|\App\Support\Providers\Group $groupProvider
     * @return \Illuminate\Http\Response
     */
    public function index($slug, GroupProvider $groupProvider)
    {
        return response($groupProvider->getMembers($slug), Response::HTTP_OK);
    }

    /**
     * @param string $slug
     * @param string $search
     * @param \App\Contracts\Models\Group|\App\Support\Providers\Group $groupProvider
     * @return \Illuminate\Http\Response
     */
    public function search($slug, $search, GroupProvider $groupProvider)
    {
        return response($groupProvider->searchMembers($slug, $search), Response::HTTP_OK);
    }

    /**
     * @param string $slug
     * @param \App\Contracts\Models\Group|\App\Support\Providers\Group $groupProvider
     * @param \App\Http\Requests\Admin\UpdateMember $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function update($slug, GroupProvider $groupProvider, UpdateMember $request)
    {
        $groupProvider->updateMembers($slug, (object)$request->input());
        event(new PermissionEntityUpdated);

        return response(null, Response::HTTP_NO_CONTENT);
    }


}
