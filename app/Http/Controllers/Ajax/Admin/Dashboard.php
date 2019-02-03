<?php

namespace App\Http\Controllers\Ajax\Admin;

use App\Http\Controllers\Admin\Controller;
use App\Models\Views\EntityCount;
use Illuminate\Http\Response;

class Dashboard extends Controller
{

    /**
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $entityCounts = EntityCount::query()->get()->toArray();
        $counts=[];
        foreach($entityCounts as $count){
            $counts[$count['tbl']] = $count['cnt'];
        }
        return response($counts, Response::HTTP_OK);
    }


}
