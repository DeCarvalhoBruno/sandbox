<?php namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\Request;

class Auth extends LoginController
{
    use ThrottlesLogins;
    /**
     * Show the application's login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm()
    {
        return view('admin.login');
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();

        return redirect(route('admin.login'));
    }

    /**
     * Get the post register / login redirect path.
     *
     * @return string
     */
    public function redirectPath()
    {
        return route('admin.dashboard');
    }
}
