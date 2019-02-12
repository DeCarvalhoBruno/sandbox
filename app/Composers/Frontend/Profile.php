<?php namespace App\Composers\Frontend;

use App\Composers\Composer;
use App\Facades\JavaScript;

class Profile extends Composer
{
    public function compose($view)
    {
        JavaScript::putArray([
            'token' => \Session::get('jwt_token'),
            'user' => auth()->user()->only(['username'])
        ]);
    }

}