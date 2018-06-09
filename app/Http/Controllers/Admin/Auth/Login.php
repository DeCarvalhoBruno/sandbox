<?php namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class Login extends LoginController
{

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
            'user' => auth()->user()->only(['username','first_name','last_name']),
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $expiration - time(),
        ];
    }


    public function logout(Request $request)
    {
        $this->guard()->logout();
        return response(null, Response::HTTP_NO_CONTENT);
    }

}