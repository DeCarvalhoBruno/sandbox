<?php

namespace App\Http\Controllers\Ajax\Admin;

use App\Contracts\Models\Media as MediaInterface;
use App\Exceptions\DiskFolderNotFoundException;
use App\Http\Controllers\Controller;
use App\Models\Entity;
use App\Support\Media\Image;
use Illuminate\Http\Response;
use App\Models\Media\Media as MediaModel;

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
     * @throws \App\Exceptions\DiskFolderNotFoundException
     * @throws \ReflectionException
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

            $media = new \App\Support\Media\Media(
                $file,
                $input->target,
                $input->type,
                $input->media
            );

            switch ($input->media) {
                case "image_avatar":
//                    $media->processAvatar();
                    $this->mediaRepo->saveAvatar(Entity::getConstant($input->type),$input->target);
                    break;
                default:
                    break;
            }
        }
        return response(null, Response::HTTP_NO_CONTENT);
    }

}