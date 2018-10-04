<?php namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Frontend\Controller;

class Home extends Controller
{

    public function index()
    {
        return view('frontend.home');
    }

}