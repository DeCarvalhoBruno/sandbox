<?php namespace Naraki\Forum\Providers;

use App\Support\Providers\Model;
use Naraki\Forum\Contracts\Post as PostInterface;

class Post extends Model implements PostInterface
{
    protected $model = \Naraki\Forum\Models\ForumPost::class;

}