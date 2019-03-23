<?php namespace Naraki\Forum\Models;

use App\Models\Entity;
use App\Support\NestedSet\NodeTrait;
use App\Traits\Models\HasASlugColumn;
use App\Traits\Models\Presentable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\JoinClause;

class ForumPost extends Model
{
    use HasASlugColumn, Presentable, NodeTrait;

    protected $primaryKey = 'forum_post_id';
    protected $slugColumn = 'forum_post_slug';
    protected $presenter = null;

    protected $fillable = [
        'entity_type_id',
        'post_user_id',
        'forum_post',
        'forum_post_slug'
    ];

    public function getUpdatedAtAttribute($value)
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $value)->diffForHumans();
    }

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
     * @param int $boardID
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
     * @param int $entityTypeId
     * @param int $entityId
     * @return \Illuminate\Database\Eloquent\Builder $query
     */
    public function scopeEntityType(Builder $query, $entityId = Entity::BLOG_POSTS, $entityTypeId = null)
    {
        return $query->join('entity_types', function (JoinClause $q) use ($entityTypeId, $entityId) {
            $q->on('entity_types.entity_type_id', '=', 'entity_types.entity_type_id');
            $q->where('entity_id', $entityId);
            if (!is_null($entityTypeId)) {
                $q->where('entity_types.entity_type_id', '=', $entityTypeId);
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
            'users.user_id', '=', 'forum_posts.post_user_id')
            ->join('people', 'people.user_id', '=', 'users.user_id');
    }


}
