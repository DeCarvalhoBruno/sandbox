<?php

namespace App\Http\Controllers\Frontend\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Support\Providers\User as UserProvider;

class Login extends Controller
{
    use AuthenticatesUsers;

    public function index()
    {
        return view('website.auth.login');
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

    public function activate($token, UserProvider $userRepo)
    {
        if (strlen($token) != 32 || !ctype_xdigit($token)) {
            return view('website.auth.activation_error');
        }
        $userRepo->activate($token);
        return redirect(route_i18n('login'))->with('status', 'activated');
    }

}
