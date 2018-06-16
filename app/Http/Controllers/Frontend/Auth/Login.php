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
        return redirect(route('home'));

    }


}
