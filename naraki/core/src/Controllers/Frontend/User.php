<?php namespace Naraki\Core\Controllers\Frontend;

use Illuminate\Http\Request;

class User extends Controller
{
    public function show($slug)
    {

    }

    public function delete(Request $request)
    {
        $user = auth()->user()->toArray();
        \Auth::guard('web')->logout();
        \Auth::guard('jwt')->logout();
        $request->session()->invalidate();
    }

}