<?php namespace App\Http\Controllers\Admin;

use App\Facades\JavaScript;
use App\Http\Controllers\Controller;

class Admin extends Controller
{

    public function index()
    {
        $config = [
            'appName' => config('app.name'),
            'locale' => app()->getLocale(),
            'locales' => config('app.locales'),
            'csrfToken' => csrf_token(),
            'user' => auth()->user()
        ];
        JavaScript::put($config);
        return view('admin.layouts.default');

    }

}