<?php

namespace App\Http\Controllers\Ajax\Admin\Settings;

use App\Http\Requests\Admin\UpdateUser;
use App\Http\Controllers\Controller;
use App\Providers\Models\User as UserProvider;
use Illuminate\Http\Response;

class Profile extends Controller
{
    /**
     * Update the user's profile information.
     *
     * @param \App\Http\Requests\Admin\UpdateUser $request
     * @param \App\Providers\Models\User $user
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUser $request, UserProvider $user)
    {

//        $savedUser = auth()->user()->update($user->filterFillables($request->all()));
//        return response($request->all(), Response::HTTP_OK);
        $savedUser = $user->updateOneByUsername(
            auth()->user()->getAttribute('username'),
            $request->all()
        );
        return response($savedUser, Response::HTTP_OK);
    }
}
