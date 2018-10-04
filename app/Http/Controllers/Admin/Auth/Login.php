<?php namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Admin\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class Login extends Controller
{
    use AuthenticatesUsers;

//    public function __construct()
//    {
//        auth()->setDefaultDriver('jwt');
//    }
//
    protected function guard()
    {
        return \Auth::guard('jwt');
    }

    protected function attemptLogin(Request $request)
    {
        $token = $this->guard()->attempt($this->credentials($request));

        if ($token) {
            $this->guard()->setToken($token);
            return true;
        }
        return false;
    }

    protected function sendLoginResponse(Request $request)
    {
        $this->clearLoginAttempts($request);

        $token = (string)$this->guard()->getToken();
        $expiration = $this->guard()->getPayload()->get('exp');
        return [
            'user' => auth()->user()->only(['username', 'first_name', 'last_name']),
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $expiration - time(),
        ];
    }

    /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function validateLogin(Request $request)
    {
        $request->validate([
            $this->username() => 'required|string',
            'password' => 'required|string',
        ]);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response|\Symfony\Component\HttpFoundation\Response
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();
        return response(null, Response::HTTP_NO_CONTENT);
    }


}