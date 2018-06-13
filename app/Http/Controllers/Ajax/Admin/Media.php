<?php

namespace App\Http\Controllers\Ajax\Admin;

use App\Contracts\Models\Media as MediaInterface;
use App\Support\Providers\User as UserProvider;
use App\Http\Controllers\Controller;
use App\Models\Entity;
use App\Models\Media\Media as MediaModel;
use App\Support\Media\UploadedImage;
use Illuminate\Http\Response;

class Media extends Controller
{
    /**
     * @var \App\Support\Providers\Media
     */
    private $mediaRepo;

    public function __construct(MediaInterface $mediaRepo)
    {
        parent::__construct();
        $this->mediaRepo = $mediaRepo;
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function add()
    {
//        dd($r->all());
        $input = (object)$this->request->only(['type', 'target', 'media', 'group', 'category', 'file']);

        $file = $this->request->file('file');

        if (!is_null($file)) {
            //Type is users, forum posts, etc.
            if (!Entity::isValidName($input->type)) {
                throw new \UnexpectedValueException('This image entity type does not match anything on disk');
            }
            //Media is image, image_avatar, etc.
            if (!MediaModel::isValidName($input->media)) {
                throw new \UnexpectedValueException('This media type does not match anything on disk');
            }

            $media = new UploadedImage(
                $file,
                $input->target,
                $input->type,
                $input->media
            );

            switch ($input->media) {
                case "image_avatar":
                    $media->saveTemporaryAvatar();
                    break;
                default:
                    break;
            }
            return response([
                'filename' => $media->getHddFilename(),
            ], Response::HTTP_OK);
        }
        return response([
            null
        ], Response::HTTP_INTERNAL_SERVER_ERROR);

    }


    public function crop(UserProvider $user)
    {
        $input = (object)$this->request->only(['uuid', 'height', 'width', 'x', 'y']);

        /**
         * @var \App\Support\Media\SimpleImage $avatarInfo
         */
        $avatarInfo = \Cache::get('temporary_avatars')->pull(substr($input->uuid, 0, 32));

        $avatarInfo->cropAvatar($input);
        $this->mediaRepo->image()->saveAvatar($avatarInfo);

        return response($user->getAvatars(auth()->user()->getKey()), Response::HTTP_OK);

    }


}