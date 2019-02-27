<?php

namespace App\Http\Controllers\Frontend\Auth;

use App\Contracts\Models\User as UserProvider;
use App\Http\Controllers\Frontend\Controller;
use Firebase\JWT\JWT;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User;

class OAuth extends Controller
{
    use AuthenticatesUsers;


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
        return Socialite::driver($provider)->stateless()->redirect()->getTargetUrl();
    }

    /**
     * Obtain the user information from the provider.
     *
     * @param  string $provider
     * @param \Illuminate\Http\Request $request
     * @param \App\Contracts\Models\User|\App\Support\Providers\User $userRepo
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback($provider, Request $request, UserProvider $userRepo)
    {
        $socialiteUser = Socialite::driver($provider)->stateless()->user();
        $this->processUser($request, $userRepo, $provider, $socialiteUser);

        return view('frontend.oauth.callback');
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Contracts\Models\User|\App\Support\Providers\User $userRepo
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function googleYolo(Request $request, UserProvider $userRepo)
    {
        $provider = 'google';
        $input = $request->only(['domain', 'full_name', 'email', 'avatar', 'google_token']);
        $jwt = new JWT();
        //Default leeway defaults to 1 second which is insufficient to do the token check in time.
        $jwt::$leeway = 10;
        $client = new \Google_Client([
            'client_id' => env('OAUTH_GOOGLE_CLIENT_ID'),
            'jwt' => $jwt
        ]);

        $tokenContents = $client->verifyIdToken($input['google_token']);
        if ($tokenContents !== false) {
            $socialiteUser = (new User)->setRaw($input)->map([
                'id' => $tokenContents['sub'],
                'nickname' => Arr::get($tokenContents, 'full_name'),
                'name' => Arr::get($tokenContents, 'full_name'),
                'email' => Arr::get($tokenContents, 'email'),
                'avatar' => Arr::get($tokenContents, 'avatar'),
            ]);
//            $socialiteUser->setExpiresIn($tokenContents['exp']);
            $this->processUser($request, $userRepo, $provider, $socialiteUser);
            \Cookie::queue('google_verified',true,(60*60*24*365/60));
            return response(null, Response::HTTP_NO_CONTENT);
        } else {
            return response(null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Contracts\Models\User|\App\Support\Providers\User $userRepo
     * @param string $provider
     * @param \Laravel\Socialite\Two\User $socialiteUser
     */
    private function processUser($request, $userRepo, $provider, $socialiteUser)
    {
        $user = $userRepo->processViaOAuth($provider, $socialiteUser);

        $request->session()->regenerate();

        \Auth::guard('jwt')->login($user);
        \Session::put('jwt_token', \Auth::guard('jwt')->login($user));
        $this->guard()->login($user, true);
        $this->clearLoginAttempts($request);
    }

}
