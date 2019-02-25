<?php

namespace App\Http\Controllers\Frontend\Auth;

use App\Http\Controllers\Frontend\Controller;
use App\Exceptions\EmailTakenException;
use App\Models\OAuthProvider;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use App\Contracts\Models\User as UserProvider;
use Laravel\Socialite\Two\User;

class OAuth extends Controller
{


    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard('web');
    }

    /**
     * Redirect the user to the provider authentication page.
     *
     * @param  string $provider
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirectToProvider($provider)
    {
        return redirect(Socialite::driver($provider)
            ->stateless()->redirect()->getTargetUrl());
    }

    /**
     * Obtain the user information from the provider.
     *
     * @param  string $provider
     * @param \Illuminate\Http\Request $request
     * @param \App\Contracts\Models\User|\App\Support\Providers\User $userRepo
     * @return \Illuminate\Http\Response
     * @throws \App\Exceptions\EmailTakenException
     */
    public function handleProviderCallback($provider, Request $request, UserProvider $userRepo)
    {
        $user = Socialite::driver($provider)->stateless()->user();
        $user = $userRepo->processViaOAuth($provider, $user);

        $request->session()->regenerate();

        $this->guard()->login(\Auth::guard('jwt')->user());

        $this->clearLoginAttempts($request);

        return redirect()->intended(route_i18n('home'));

        return view('oauth/callback');
    }

}
