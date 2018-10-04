<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @var \Illuminate\Http\Request
     */
    protected $request;
    /**
     * @var \App\Models\User
     */
    protected $user;

    public function __construct()
    {
        auth()->setDefaultDriver('web');
        $this->user = auth()->user();
        $this->request = app('request');
    }
}
