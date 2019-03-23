<?php namespace Naraki\Forum\Providers;

use App\Support\Providers\Model;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Naraki\Forum\Contracts\Post as PostInterface;

class Post extends Model implements PostInterface
{
    protected $model = \Naraki\Forum\Models\ForumPost::class;

    /**
     * @param int $entityTypeId
     * @return \Illuminate\Database\Eloquent\Builder
     * @param \Illuminate\Contracts\Auth\Authenticatable|\App\Models\User $user
     */
    public function buildComments(int $entityTypeId, Authenticatable $user): Builder
    {
        return $this->buildWithScopes(
            [
                'forum_post_slug as slug',
                'forum_post as txt',
                'username',
                'full_name as name',
                'forum_posts.updated_at'
            ],
            ['entityType', 'user'])
            ->where('entity_types.entity_type_id', $entityTypeId)
            ->where('users.user_id', $user->getKey());
    }

    /**
     * @param string $comment
     * @param int $entityTypeId
     * @param \Illuminate\Contracts\Auth\Authenticatable|\App\Models\User $user
     * @return void
     */
    public function addComment(string $comment, int $entityTypeId, Authenticatable $user)
    {
        try {
            $post = $this->build()->create([
                'entity_type_id' => $entityTypeId,
                'post_user_id' => $user->getKey(),
                'forum_post' => $comment,
                'forum_post_slug' => sprintf('%s_%s',
                    mb_substr($user->getAttribute('person_slug'), 0, 31),
                    makeHexUuid()
                )
            ])->save();
        } catch (\Exception $e) {

        }


    }

}