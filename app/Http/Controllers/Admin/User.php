<?php

namespace App\Http\Controllers\Admin;

use App\Facades\JavaScript;

class User extends Admin
{
    public function index()
    {
//        JavaScript::put('something special', 'my titties');
        return view('admin.layouts.default');
    }

}
