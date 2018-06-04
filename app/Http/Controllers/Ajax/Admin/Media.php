<?php

namespace App\Http\Controllers\Ajax\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

class Media extends Controller
{
    public function add()
    {
        return response(null, Response::HTTP_NO_CONTENT);
    }
    
}