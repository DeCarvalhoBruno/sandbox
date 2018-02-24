<?php namespace App\Http\Controllers\Admin;

use App\Filters\User as UserFilter;
use App\Http\Controllers\Controller;
use App\Models\User;
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
//        (new \App\Support\Permissions\User);
        //dd(Model::getLastID(PermissionStore::class));
        return view('admin.layouts.test');
    }

}