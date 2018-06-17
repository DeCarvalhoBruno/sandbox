<?php namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;

class Home extends Controller
{

    /**
     *
     */
    public function __construct()
    {
        $this->middleware('web');
    }

    public function index()
    {
//        dd(auth()->guard());
        return view('home');
    }

}