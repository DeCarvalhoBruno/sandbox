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
        $status = \Session::get('status');
        return view('website.auth.login',compact('status'));
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
        if (is_hex_uuid_string($token)) {
            $nbDeletedRecords = $userRepo->activate($token);
            if ($nbDeletedRecords === 1) {
                return redirect(route_i18n('login'))->with('status', 'activated');
            }
        }
        return view('website.auth.activation_error');
    }

}
