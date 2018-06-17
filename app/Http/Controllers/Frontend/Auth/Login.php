<?php

namespace App\Http\Controllers\Frontend\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class Login extends Controller
{
    use AuthenticatesUsers;

    public function index()
    {
        return view('auth.login');
    }

    public function sendLoginResponse($request)
    {
        return redirect(route_i18n('home'));
    }

    public function logout()
    {
        $this->guard()->logout();
        $this->request->session()->invalidate();
        return redirect(route_i18n('home'));
    }

}
