<?php namespace App\Http\Controllers\Admin;

use App\Filters\User as UserFilter;
use App\Http\Controllers\Controller;
use App\Providers\Models\User as UserProvider;

class Admin extends Controller
{

    public function index()
    {
        return view('admin.layouts.default');
    }

    public function test(UserProvider $userProvider, UserFilter $userFilter)
    {
        return view('admin.layouts.test');
    }

}