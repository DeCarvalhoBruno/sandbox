<?php namespace App\Http\Controllers\Admin;

use App\Filters\User as UserFilter;
use App\Providers\Models\User as UserProvider;
use App\Http\Controllers\Controller;
use App\Models\Person;

class Admin extends Controller
{

    public function index()
    {
        return view('admin.layouts.default');

    }

    public function test(UserProvider $userProvider, UserFilter $userFilter)
    {
        $f = $userProvider->select([
            "full_name as name",
            'users.user_id as url',
            'created_at',
            'email'
        ])->activated()->filter($userFilter)->limit(10)->get()->toArray();

        dd($f);
        return view('admin.layouts.test');
    }

}