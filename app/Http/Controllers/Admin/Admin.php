<?php namespace App\Http\Controllers\Admin;

use App\Filters\User as UserFilter;
use App\Http\Controllers\Controller;
use App\Providers\Models\User as UserProvider;

class Admin extends Controller
{

    public function index()
    {
//        dd(app('router')->getCurrentRoute());
        return view('admin.layouts.default');

    }

    public function test(UserProvider $userProvider, UserFilter $userFilter)
    {
        $perms = (new \App\Support\Permissions\User)->getPermissionsToInsert();
        dd($perms);

        return view('admin.layouts.test');
    }

}