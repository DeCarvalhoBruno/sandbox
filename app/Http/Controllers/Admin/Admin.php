<?php namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class Admin extends Controller
{

    public function index()
    {
        return view('admin.layouts.default');

    }

    public function test()
    {
        dd(\App\Models\SystemEntity::getModelPrimaryKey(\App\Models\SystemEntity::PEOPLE));

    }

}