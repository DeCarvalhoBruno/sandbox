<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Sandbox extends Controller
{
    public function get(Request $r)
    {

        return view('backend.layouts.app');
    }
}
