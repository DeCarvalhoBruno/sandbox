<?php namespace App\Http\Controllers\Admin;

use Illuminate\Foundation\Auth\ThrottlesLogins;

class Auth
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
     * Get the post register / login redirect path.
     *
     * @return string
     */
    public function redirectPath()
    {
        return route_18n('admin.dashboard');
    }
}
