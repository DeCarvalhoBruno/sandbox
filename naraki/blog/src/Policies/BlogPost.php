<?php namespace Naraki\Blog\Policies;

use App\Policies\Policy;

class BlogPost extends Policy
{
    protected $model = \Naraki\Blog\Models\BlogPost::class;

}
