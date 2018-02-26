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
//        (new \App\Support\Permissions\User)->assignPermissions();
//        (new \App\Support\Permissions\Group)->assignPermissions();

//        $f=PermissionMask::getDefaultPermission(7,Entity::USERS);
//        dd($f);
//        dd(5&\App\Models\User::PERMISSION_ADD);
        return view('admin.layouts.test');
    }

}