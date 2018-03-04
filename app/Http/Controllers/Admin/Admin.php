<?php namespace App\Http\Controllers\Admin;

use App\Contracts\RawQueries;
use App\Filters\User as UserFilter;
use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\User;
use App\Providers\Models\User as UserProvider;

class Admin extends Controller
{

    public function index()
    {
        return view('admin.layouts.default');
    }

    public function test(UserProvider $userProvider, UserFilter $userFilter)
    {
//        $f = app()->make(RawQueries::class);
//        dd($f->getAllUserPermissions(auth()->user()->getEntityType()));
//        $s=new Group();
//        dd($s->getReadablePermissions(5));

        return view('admin.layouts.test');
    }

}