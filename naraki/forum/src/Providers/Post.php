<?php namespace Naraki\Forum\Providers;

use App\Support\Providers\Model;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Naraki\Forum\Contracts\Post as PostInterface;

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
            ],
            ['entityType', 'user','tree'])
            ->where('entity_types.entity_type_id', $entityTypeId);
    }

    /**
     * @param \stdClass $commentData
     * @param int $entityTypeId
     * @param \Illuminate\Contracts\Auth\Authenticatable|\App\Models\User $user
     * @return void
     */
    public function addComment(\stdClass $commentData, int $entityTypeId, Authenticatable $user)
    {
        try {
            $parentPost = null;
            if (isset($commentData->reply_to)) {
                $model = $this->createModel();
                /**
                 * @var \Naraki\Forum\Models\ForumPost $parentPost
                 */
                $parentPost = $model->newQuery()
                    ->select([$model->getKeyName(),$model->getRgtName(),$model->getLftName()])
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

    public function getTree()
    {


    }

}