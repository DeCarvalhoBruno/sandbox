<?php namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Admin extends Controller
{

    public function index()
    {
        return view('admin.layouts.default');
    }

    public function test()
    {
        return view('admin.layouts.test');
    }

}