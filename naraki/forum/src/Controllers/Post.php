<?php namespace Naraki\Forum\Controllers;

use App\Http\Controllers\Frontend\Controller;
use App\Models\Entity;
use App\Models\EntityType;
use Illuminate\Http\Response;
use Naraki\Forum\Facades\Forum;
use Naraki\Forum\Requests\CreateComment;
use Naraki\Media\Models\Media;
use Naraki\Media\Models\MediaEntity;
use Naraki\Media\Models\MediaImgFormat;
use Naraki\Forum\Support\Trees\Post as PostTree;

class Post extends Controller
{
    public function getComments($type, $slug)
    {
        $entityTypeId = EntityType::getEntityTypeID(Entity::getConstant($type), $slug);
        $postCollection = Forum::post()->buildComments($entityTypeId)
            ->orderBy('root_updated_at', 'desc')->get();
        $postUsers = $postCollection->pluck('username');
        $dbPosts = $postCollection->toArray();
        if (!empty($dbPosts)) {
            unset($postCollection);
            $userMedia = EntityType::getEntityInfoFromSlug(Entity::USERS, $postUsers)
                ->scopes(['avatars'])->select(['username', 'media_uuid as uuid', 'media_extension as ext'])->get();
            $media = [];
            foreach ($userMedia as $m) {
                $media[$m->getAttribute('username')] = $m;
            }
            foreach ($dbPosts as $key => $user) {
                $dbPosts[$key]['name'] = route_i18n('user', ['slug' => $dbPosts[$key]['username']]);
                if (isset($media[$user['username']])) {
                    $dbPosts[$key]['media'] = MediaEntity::assetStatic(
                        Entity::USERS,
                        Media::IMAGE_AVATAR,
                        $media[$user['username']]['uuid'],
                        $media[$user['username']]['ext'], MediaImgFormat::ORIGINAL);

                } else {
                    $dbPosts[$key]['media'] = null;
                }
            }
        }
        $user = auth()->user();
        $posts = PostTree::getTree($dbPosts,(!is_null($user)?$user->getKey():null));
        return response(compact('posts'), 200);
    }

    public function postComment($type, $slug, CreateComment $request)
    {
        $input = $request->all();
        $entityTypeId = EntityType::getEntityTypeID(Entity::getConstant($type), $slug);
        Forum::post()->addComment(
            (object)$input, $entityTypeId, auth()->user()
        );

        return response([
            'type' => 'success',
            'title' => (!$request->has('reply_to')
                ? trans('messages.comment_add_success')
                : trans('messages.reply_add_success')
            )
        ], Response::HTTP_OK);
    }

}