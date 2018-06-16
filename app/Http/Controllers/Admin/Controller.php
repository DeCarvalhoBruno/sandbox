<?php namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller as BaseController;

class Controller extends BaseController
{
    public function __construct()
    {
        auth()->setDefaultDriver('jwt');
        parent::__construct();
    }

}