<?php

namespace App\Http\Controllers\Ajax\Admin\Settings;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use App\Support\Providers\User as UserProvider;

class General extends Controller
{
    /**
     * Update the user's password.
     *
     * @param  \Illuminate\Http\Request $request
     * @param \App\Support\Providers\User $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, UserProvider $user)
    {


        return response('', Response::HTTP_NO_CONTENT);
    }
}
