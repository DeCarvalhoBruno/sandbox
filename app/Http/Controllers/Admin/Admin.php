<?php namespace App\Http\Controllers\Admin;

class Admin extends Controller
{

    public function index()
    {
        return view('admin.default');
    }

}