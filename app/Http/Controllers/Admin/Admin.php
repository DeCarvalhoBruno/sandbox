<?php namespace App\Http\Controllers\Admin;

class Admin extends Controller
{

    public function index()
    {
        return view('admin.default');
    }

    public function test()
    {
        return view('admin.default');
    }

}