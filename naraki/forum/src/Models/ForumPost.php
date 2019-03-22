<?php namespace Naraki\Forum\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\JoinClause;

class ForumPost extends Model
{
    protected $primaryKey = 'forum_post_id';

    protected $fillable = [
        'entity_type_id',
        'post_user_id',
        'forum_post'
    ];

    /**
     * @link https://laravel.com/docs/eloquent#query-scopes
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $threadID
     *
     * @return \Illuminate\Database\Eloquent\Builder $query
     */
    public function scopeThread(Builder $query, $threadID = null)
    {
        return $query->join('forum_threads', function (JoinClause $q) use ($threadID) {
            $q->on('forum_threads.forum_thread_id', '=', 'forum_posts.forum_thread_id');
            if (!is_null($threadID)) {
                $q->where('forum_threads.forum_thread_id', $threadID);
            }
        });
    }

    /**
     * @link https://laravel.com/docs/eloquent#query-scopes
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @param null $boardID
     * @return \Illuminate\Database\Eloquent\Builder $query
     */
    public function scopeBoard(Builder $query, $boardID = null)
    {
        return $query->join('forum_boards', function (JoinClause $q) use ($boardID) {
            $q->on('forum_boards.forum_board_id', '=', 'forum_threads.forum_board_id');
            if (!is_null($boardID)) {
                $q->where('forum_boards.forum_board_id', '=', $boardID);
            }
        });
    }

    /**
     * @link https://laravel.com/docs/eloquent#query-scopes
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder $query
     */
    public function scopeUser(Builder $query)
    {
        return $query->join('users',
            'users.id', '=', 'forum_posts.post_user_id');
    }

}
