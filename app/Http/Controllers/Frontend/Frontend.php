<?php namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Blog\BlogPost;
use App\Models\Entity;
use App\Support\Permissions\Generic;

class Frontend extends Controller
{
    public function test()
    {
        $f = new Generic(BlogPost::class,Entity::BLOG_POSTS);
        $f->assignPermissions();


        
    }

}