<?php namespace Naraki\Forum\Providers;

use App\Support\Providers\Model;
use Naraki\Forum\Contracts\Board as BoardInterface;

class Board extends Model implements BoardInterface
{
    protected $model = \Naraki\Forum\Models\ForumBoard::class;


}