<?php

namespace Naraki\Core\Controllers\Frontend\Auth;

use Naraki\Sentry\Events\UserRegistered;
use Naraki\Core\Controllers\Frontend\Controller;
use App\Http\Requests\Frontend\CreateUser;
use Naraki\Sentry\Providers\User as UserProvider;

class Register extends Controller
{
    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showRegistrationForm()
    {
        return view('frontend.auth.register');
    }

    /**
     * Handle a registration request for the application.
     *
     * @param \App\Http\Requests\Frontend\CreateUser $request
     * @param \Naraki\Sentry\Providers\User $userRepo
     * @return \Illuminate\Http\Response
     */
    public function register(CreateUser $request, UserProvider $userRepo)
    {
        $user = $userRepo->createOne($request->except(['timezone']));
        $userRepo->updateStats($user,$request->only(['stat_user_timezone']));

        event(new UserRegistered($user, $userRepo->generateActivationToken($user)));
        return redirect(route_i18n('login'))->with('status', 'registered');
    }

}
