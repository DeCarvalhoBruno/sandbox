<?php namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;

class Home extends Controller
{
    public function index()
    {
        return view('home');
    }

}