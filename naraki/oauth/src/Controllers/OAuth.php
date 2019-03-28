<?php namespace Naraki\Oauth\Controllers;

use App\Contracts\Models\User as UserProvider;
use App\Http\Controllers\Frontend\Controller;
use Firebase\JWT\JWT;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Naraki\Oauth\Exceptions\EmailNotVerified;
use Naraki\Oauth\Socialite\GoogleUser;

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
        \Cookie::queue('google_verified', true, 60 * 60 * 24 * 365);

        return view('oauth::callback');
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Contracts\Models\User|\App\Support\Providers\User $userRepo
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function googleYolo(Request $request, UserProvider $userRepo)
    {
        $provider = 'google';
        $input = $request->only(['google_token','avatar']);
        $jwt = new JWT();
        //Default leeway defaults to 1 second which is insufficient to do the token check in time.
        $jwt::$leeway = 10;
        $client = new \Google_Client([
            'client_id' => config('services.google.client_id'),
            'jwt' => $jwt
        ]);
        $tokenContents = $client->verifyIdToken($input['google_token']);
        if ($tokenContents !== false) {
            $tokenContents['picture'] = $input['avatar'];
            try {
                $this->processUserYolo($request, $userRepo, $provider, new GoogleUser($tokenContents));
            } catch (EmailNotVerified $e) {
                return response('email not verified', Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            \Cookie::queue('google_verified', true, 60 * 60 * 24 * 365);
            return response(null, Response::HTTP_NO_CONTENT);
        } else {
            return response(null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Contracts\Models\User|\App\Support\Providers\User $userRepo
     * @param string $provider
     * @param \Naraki\Oauth\Socialite\GoogleUser $socialiteUser
     */
    private function processUserYolo($request, $userRepo, $provider, GoogleUser $socialiteUser)
    {
        $user = $userRepo->processViaYolo($provider, $socialiteUser);
        $this->login($request, $user);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Contracts\Models\User|\App\Support\Providers\User $userRepo
     * @param string $provider
     * @param \Laravel\Socialite\Two\User $socialiteUser
     */
    private function processUser($request, $userRepo, $provider, SocialiteUser $socialiteUser)
    {
        $user = $userRepo->processViaOAuth($provider, $socialiteUser);
        $this->login($request, $user);

    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param $user
     */
    private function login($request, $user)
    {
        if (env('APP_ENV') !== 'testing') {
            $request->session()->regenerate();
        }

        \Auth::guard('jwt')->login($user);
        \Session::put('jwt_token', \Auth::guard('jwt')->login($user));
        $this->guard()->login($user, true);
        $this->clearLoginAttempts($request);
    }

}
