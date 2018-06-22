<?php namespace App\Http\Controllers\Ajax\Admin;

use App\Http\Controllers\Controller;
use App\Filters\Blog as BlogFilter;

class Blog extends Controller
{
    /**
     * @param \App\Filters\Blog $filter
     * @return array
     */
    public function index(BlogFilter $filter)
    {
        return [
            'table' => \App\Models\Blog\BlogPost::query()
                ->select([
                    'blog_post_title as ' . trans('ajax.db_raw_inv.blog_post_title'),
                ])
                ->filter($filter)->paginate(10),
            'columns' => (new \App\Models\Group)->getColumnInfo([
                trans('ajax.db_raw_inv.blog_post_title') => trans('ajax.db.group_name')
            ])
        ];

    }

}