<?php namespace App\Http\Controllers\Ajax\Admin\Auth;

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class Login extends LoginController
{

    public function logout(Request $request)
    {
        $this->guard()->logout();
        return response(null, Response::HTTP_NO_CONTENT);
    }
}