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
        $counts = [];
        $color = [
            'light-blue',
            'red',
            'green',
            'aqua',
            'yellow',
            'blue',
            'navy',
            'teal',
            'olive',
            'lime',
            'orange',
            'fuchsia',
            'purple',
            'maroon',
        ];
        $icon = [
            'users' => 'user',
            'groups' => 'users',
            'blog_posts' => 'newspaper',
            'medias' => 'image',
        ];
        foreach ($entityCounts as $count) {
            $counts[$count['tbl']] = [
                'count' => $count['cnt'],
                'color' => $color[rand(0, 13)],
                'icon' => $icon[$count['tbl']]
            ];
        }
        return response($counts, Response::HTTP_OK);
    }
}
