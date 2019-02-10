<?php namespace App\Http\Controllers\Frontend;

class Home extends Controller
{

    public function index()
    {
        return view('frontend.site.home');
    }

}