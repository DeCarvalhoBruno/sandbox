<?php namespace Naraki\Forum\Controllers;

use App\Http\Controllers\Frontend\Controller;
use App\Models\Entity;
use App\Models\EntityType;
use Illuminate\Http\Response;
use Naraki\Forum\Facades\Forum;
use Naraki\Forum\Requests\CreateComment;
use Naraki\Media\Models\Media;
use Naraki\Media\Models\MediaEntity;

class Post extends Controller
{
    public function getComments($type, $slug)
    {
        $entityTypeId = EntityType::getEntityTypeID(Entity::getConstant($type), $slug);

        $postCollection = Forum::post()->buildComments($entityTypeId, auth()->user())->get();
        $postUsers = $postCollection->pluck('username');
        $posts = $postCollection->toArray();
        unset($postCollection);
        $userMedia = EntityType::getEntityInfoFromSlug(Entity::USERS, $postUsers)
            ->scopes(['avatars'])->select(['username', 'media_uuid as uuid', 'media_extension as ext'])->get();
        $media = [];
        foreach ($userMedia as $m) {
            $media[$m->getAttribute('username')] = $m;
        }
        foreach ($posts as $key => $user) {
            $posts[$key]['name'] = route_i18n('user', ['slug' => $posts[$key]['username']]);
            if (isset($media[$user['username']])) {
                $posts[$key]['media'] = MediaEntity::assetStatic(
                    Entity::USERS,
                    Media::IMAGE_AVATAR,
                    $media[$user['username']]['uuid'],
                    $media[$user['username']]['ext']);

            } else {
                $posts[$key]['media'] = null;
            }
        }

        return response(compact('posts'), 200);
    }

    public function postComment($type, $slug, CreateComment $request)
    {
        $input = $request->all();
        $entityTypeId = EntityType::getEntityTypeID(Entity::getConstant($type), $slug);
        Forum::post()->addComment(
            $input['txt'], $entityTypeId, auth()->user()
        );


        return response(null, Response::HTTP_NO_CONTENT);
    }

}