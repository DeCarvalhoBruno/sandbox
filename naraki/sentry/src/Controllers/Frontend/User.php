<?php namespace Naraki\Sentry\Controllers\Frontend;

use Illuminate\Http\Request;
use Naraki\Core\Controllers\Frontend\Controller;

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