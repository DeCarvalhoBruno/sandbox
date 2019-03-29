<?php namespace Naraki\Forum\Providers;

use App\Support\Providers\Model;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Naraki\Forum\Contracts\Post as PostInterface;
use Naraki\Forum\Support\Trees\Post as PostTree;

/**
 * @method \Naraki\Forum\Models\ForumPost createModel(array $attributes = [])
 * @method \Naraki\Forum\Models\ForumPost getOne($id, $columns = ['*'])
 */
class Post extends Model implements PostInterface
{
    protected $model = \Naraki\Forum\Models\ForumPost::class;

    /**
     * @param int $entityTypeId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function buildComments(int $entityTypeId): Builder
    {
        return $this->buildWithScopes(
            [
                'forum_posts.forum_post_id as id',
                'lvl',
                'forum_post_slug as slug',
                'forum_post as txt',
                'username',
                'post_user_id as user_id',
                'full_name as name',
                'forum_posts.updated_at',
                'forum_post_fav_cnt as cnt'
            ],
            ['entityType', 'user', 'tree'])
            ->where('entity_types.entity_type_id', $entityTypeId);
    }

    /**
     * @param \stdClass $commentData
     * @param int $entityTypeId
     * @param \Illuminate\Contracts\Auth\Authenticatable|\App\Models\User $user
     * @return void
     */
    public function createComment(\stdClass $commentData, int $entityTypeId, Authenticatable $user)
    {
        try {
            $parentPost = null;
            if (isset($commentData->reply_to) && !is_null($commentData->reply_to)) {
                $model = $this->createModel();
                /**
                 * @var \Naraki\Forum\Models\ForumPost $parentPost
                 */
                $parentPost = $model->newQuery()
                    ->select([$model->getKeyName(), $model->getRgtName(), $model->getLftName()])
                    ->where($model->getSlugColumnName(), $commentData->reply_to)
                    ->first();
            }
            /**
             * @var \Naraki\Forum\Models\ForumPost $post
             */
            $post = $this->build()->create([
                'entity_type_id' => $entityTypeId,
                'post_user_id' => $user->getKey(),
                'forum_post' => $commentData->txt,
                'forum_post_slug' => sprintf('%s_%s',
                    mb_substr($user->getAttribute('person_slug'), 0, 31),
                    makeHexUuid()
                )
            ]);

            if (is_null($parentPost)) {
                $post->save();
            } else {
                $post->appendToNode($parentPost)->save();
            }
        } catch (\Exception $e) {
        }
    }

    /**
     * @param \stdClass $data
     * @param \Illuminate\Contracts\Auth\Authenticatable|\App\Models\User $user
     * @return void
     */
    public function updateComment($data, Authenticatable $user)
    {
        if (!isset($data->reply_to)) {
            return;
        }
        $model = $this->createModel();
        $q = $model->newQuery()->scopes(['entityType', 'user'])
            ->select([
                'post_user_id',
                $model->getKeyName(),
                $model->getParentIdName(),
                $model->getLftName(),
                $model->getRgtName()
            ])
            ->where($model->getSlugColumnName(), $data->reply_to)->first();
        if (!is_null($q)) {
            if ($user->getKey() == $q->getAttribute('post_user_id')) {
                $q->update(['forum_post' => $data->txt]);
            }
        }

    }

    /**
     * @param array $posts
     * @param Authenticatable|\App\Models\User $user
     * @param arrray $favorites
     * @return array
     */
    public function getTree(array $posts, ?Authenticatable $user, $favorites): array
    {
        return PostTree::getTree(
            $posts,
            !is_null($user) ? $user->getKey() : null,
            !is_null($favorites) ? $favorites : []);
    }

    /**
     * @param string $slug
     * @param Authenticatable|\App\Models\User $user
     * @return int
     * @throws \Exception
     */
    public function deleteComment(string $slug, Authenticatable $user)
    {
        $model = $this->createModel();
        $q = $model->newQuery()->scopes(['entityType', 'user'])
            ->select([
                'post_user_id',
                $model->getKeyName(),
                $model->getParentIdName(),
                $model->getLftName(),
                $model->getRgtName()
            ])
            ->where($model->getSlugColumnName(), $slug)->first();
        if (!is_null($q)) {
            if ($user->getKey() == $q->getAttribute('post_user_id')) {
                return $q->delete();
            }
        }
        return 0;
    }

    /**
     * @param string $slug
     * @return bool
     */
    public function favorite(string $slug)
    {
        return \DB::unprepared(sprintf('CALL sp_increment_forum_post_favorite_count("%s")', $slug));
    }

    /**
     * @param string $slug
     * @return bool
     */
    public function unfavorite(string $slug)
    {
        return \DB::unprepared(sprintf('CALL sp_decrement_forum_post_favorite_count("%s")', $slug));
    }

}