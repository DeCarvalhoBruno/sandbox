<?php namespace Naraki\Forum\Providers;

use App\Support\Providers\Model;
use Naraki\Forum\Contracts\thread as ThreadInterface;

class Thread extends Model implements ThreadInterface
{
    protected $model = \Naraki\Forum\Models\ForumThread::class;

}