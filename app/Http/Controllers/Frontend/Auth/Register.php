<?php

namespace App\Http\Controllers\Frontend\Auth;

use App\Events\UserRegistered;
use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\CreateUser;
use App\Support\Providers\User as UserProvider;

class Register extends Controller
{
    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showRegistrationForm()
    {
        return view('website.auth.register');
    }

    /**
     * Handle a registration request for the application.
     *
     * @param \App\Http\Requests\Frontend\CreateUser $request
     * @param \App\Support\Providers\User $userRepo
     * @return \Illuminate\Http\Response
     */
    public function register(CreateUser $request, UserProvider $userRepo)
    {
        $user = $userRepo->createOne($request->all());
        event(new UserRegistered($user, $userRepo->generateActivationToken($user)));

//        \Auth::guard()->login($user);

        return redirect(route_i18n('home'));
    }

}
