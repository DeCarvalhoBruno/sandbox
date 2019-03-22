<?php namespace Naraki\Forum\Providers;

use App\Support\Providers\Model;
use Naraki\Forum\Contracts\Post as PostInterface;

class Post extends Model implements PostInterface
{
    protected $model = \Naraki\Forum\Models\ForumPost::class;

    /**
     * @param string $comment
     * @param int $entityTypeId
     * @param int $userId
     * @return void
     */
    public function addComment(string $comment, int $entityTypeId, int $userId)
    {
        $post = $this->build()->create([
            'entity_type_id'=>$entityTypeId,
            'post_user_id'=>$userId,
            'forum_post'=>$comment
        ])->save();


    }

}