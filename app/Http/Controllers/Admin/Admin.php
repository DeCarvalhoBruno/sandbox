<?php namespace App\Http\Controllers\Admin;

use App\Filters\User as UserFilter;
use App\Http\Controllers\Controller;
use App\Providers\Models\User as UserProvider;
use Carbon\Carbon;

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