<?php namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Person;

class Admin extends Controller
{

    public function index()
    {
        return view('admin.layouts.default');

    }

    public function test()
    {
//        $f = (new \App\Models\User)->first();
        dd(\App\Models\User::first());
//dd((new \App\Models\User())->getKey());
        return view('admin.layouts.test');
    }

}