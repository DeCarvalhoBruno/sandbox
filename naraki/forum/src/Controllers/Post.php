<?php namespace Naraki\Forum\Controllers;

use App\Http\Controllers\Frontend\Controller;
use App\Models\Entity;
use App\Models\EntityType;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;
use Naraki\Forum\Facades\Forum;
use Naraki\Forum\Requests\CreateComment;
use Naraki\Media\Models\Media;
use Naraki\Media\Models\MediaEntity;
use Naraki\Media\Models\MediaImgFormat;

class Post extends Controller
{
    /**
     * @param string $type
     * @param string $slug
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function getComments($type, $slug)
    {
        $entityTypeId = EntityType::getEntityTypeID(Entity::getConstant($type), $slug);
        $favorites = Session::get('favorite_list');
        $postCollection = Forum::post()->buildComments($entityTypeId)
            ->orderBy('root_updated_at', 'desc')->get();
        $postUsers = $postCollection->pluck('username')->unique();
        $dbPosts = $postCollection->toArray();
        if (!empty($dbPosts)) {
            $userId = !is_null(auth()->user()) ? auth()->user()->getKey() : null;
            $userMedia = EntityType::getEntityInfoFromSlug(Entity::USERS, $postUsers->toArray())
                ->scopes(['avatars'])->select(['username', 'media_uuid as uuid', 'media_extension as ext'])->get();
            unset($postCollection,$postUsers);
            $media = [];
            foreach ($userMedia as $m) {
                $media[$m->getAttribute('username')] = $m;
            }
            foreach ($dbPosts as $key => $user) {
                $dbPosts[$key]['name'] = route_i18n('user', ['slug' => $dbPosts[$key]['username']]);
                $dbPosts[$key]['owns'] = $dbPosts[$key]['user_id']==$userId;
                $dbPosts[$key]['fav'] = isset($favorites[ $dbPosts[$key]['slug']]);
                $dbPosts[$key]['edit_mode'] = false;

                if (isset($media[$user['username']])) {
                    $dbPosts[$key]['media'] = MediaEntity::assetStatic(
                        Entity::USERS,
                        Media::IMAGE_AVATAR,
                        $media[$user['username']]['uuid'],
                        $media[$user['username']]['ext'], MediaImgFormat::ORIGINAL
                    );

                } else {
                    $dbPosts[$key]['media'] = null;
                }
            }
        }
        $posts = Forum::post()->getTree($dbPosts, auth()->user(), $favorites);
        return response(compact('posts'), 200);
    }

    /**
     * @param string $type
     * @param string $slug
     * @param \Naraki\Forum\Requests\CreateComment $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function createComment($type, $slug, CreateComment $request)
    {
        $entityTypeId = EntityType::getEntityTypeID(Entity::getConstant($type), $slug);
        Forum::post()->createComment(
            (object)$request->all(), $entityTypeId, auth()->user()
        );

        return response([
            'type' => 'success',
            'title' => (!$request->has('reply_to')
                ? trans('messages.comment_add_success')
                : trans('messages.reply_add_success')
            )
        ], Response::HTTP_OK);
    }

    /**
     * @param string $type
     * @param string $slug
     * @param \Naraki\Forum\Requests\CreateComment $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function updateComment($type, $slug, CreateComment $request)
    {
        Forum::post()->updateComment(
            (object)$request->all(), auth()->user()
        );
        return response([
            'type' => 'success',
            'title' => trans('messages.comment_update_success')
        ], Response::HTTP_OK);

    }

    /**
     * @param string $type
     * @param string $slug
     * @param string $commentSlug
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \Exception
     */
    public function deleteComment($type, $slug, $commentSlug)
    {
        Forum::post()->deleteComment($commentSlug, auth()->user());
        return response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @param string $type
     * @param string $slug
     * @param string $commentSlug
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function favorite($type, $slug, $commentSlug)
    {
        $userFavoriteList = Session::get('favorite_list');
        if (is_null($userFavoriteList)) {
            Session::put('favorite_list', new Collection([$commentSlug => true]));
        } else {
            $userFavoriteList->put($commentSlug, true);
            Session::put('favorite_list', $userFavoriteList);
        }
        Forum::post()->favorite($commentSlug);
        return response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @param string $type
     * @param string $slug
     * @param string $commentSlug
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function unfavorite($type, $slug, $commentSlug)
    {
        $userFavoriteList = Session::get('favorite_list');
        if (!is_null($userFavoriteList)) {
            $userFavoriteList->forget($commentSlug);
            Session::put('favorite_list', $userFavoriteList);
            Forum::post()->unfavorite($commentSlug);
        }
        return response(null, Response::HTTP_NO_CONTENT);
    }

}