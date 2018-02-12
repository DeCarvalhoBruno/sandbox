<?php

namespace App\Http\Controllers\Admin;

class User extends Admin
{
    public function index()
    {
        return view('admin.user.index');

    }

}
