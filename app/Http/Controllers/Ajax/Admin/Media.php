<?php

namespace App\Http\Controllers\Ajax\Admin;

use App\Contracts\Models\Media as MediaInterface;
use App\Models\EntityType;
use App\Models\Media\MediaImgFormat;
use App\Support\Media\UploadedImage;
use App\Support\Providers\User as UserProvider;
use App\Http\Controllers\Admin\Controller;
use App\Models\Entity;
use App\Models\Media\Media as MediaModel;
use App\Support\Media\UploadedAvatar;
use Illuminate\Http\Response;

class Media extends Controller
{
    /**
     * @var \App\Support\Providers\Media
     */
    private $mediaRepo;

    /**
     *
     * @param \App\Contracts\Models\Media|\App\Support\Providers\Media $mediaRepo
     */
    public function __construct(MediaInterface $mediaRepo)
    {
        parent::__construct();
        $this->mediaRepo = $mediaRepo;
    }

    public function edit($uuid)
    {
        $media = $this->mediaRepo->image()->getOne($uuid,
            ['media_uuid', 'media_title', 'media_alt', 'created_at', 'media_description', 'media_caption', 'media_id', 'media_extension']
        )->toArray();
        $data = $this->mediaRepo->image()->getSiblings($uuid, ['media_uuid', 'entity_type_id'])->get();
        $navData = $data->pluck('media_uuid')->all();
        $fromEntity = $data->pluck('entity_type_id')->first();
        $entityInfo = EntityType::buildQueryFromUnknownEntity($fromEntity);
        $intended = $type = null;
        if (!is_null($entityInfo)) {
            $intendedSlug = $entityInfo->query->select('blog_post_slug')->pluck('blog_post_slug')->first();
            $entity = $entityInfo->entity;
            switch ($entity) {
                case Entity::BLOG_POSTS:
                    $intended = (object)['route' => 'admin.blog.edit', 'slug' => $intendedSlug];
                    $type = Entity::getConstantName(Entity::BLOG_POSTS);
                    break;
            }
//            $mediaSrc = (object)['media' => MediaModel::getConstantName($media['media_id']), 'type' => $type];
            $media['media'] = MediaModel::getConstantName($media['media_id']);
            $media['type'] = $type;
            $media['suffix'] = MediaImgFormat::getFormatAcronyms(MediaImgFormat::THUMBNAIL);
        }

        $index = array_search($uuid, $navData);
        $total = count($navData);
        $nav = array(
            'total' => $total,
            'idx' => ($index + 1),
            'first' => $navData[0],
            'last' => $navData[($total - 1)],
            'prev' => ($navData[$index - 1] ?? null),
            'next' => ($navData[$index + 1] ?? null)
        );
        return [
            'media' => $media,
            'nav' => $nav,
            'intended' => $intended,
        ];
    }

    /**
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function add()
    {
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

            try {
                switch ($input->media) {
                    case "image_avatar":
                        $media = new UploadedAvatar(
                            $file,
                            $input->target,
                            $input->type,
                            $input->media
                        );
                        $media->saveTemporaryAvatar();
                        break;
                    case "image":
                        $media = new UploadedImage(
                            $file,
                            $input->target,
                            $input->type,
                            $input->media
                        );
                        return response(
                            $this->mediaRepo->image()->getImages(
                                $this->processImage($media), [
                                'media_uuid as uuid',
                                'media_in_use as used',
                                'media_extension as ext',
                                \DB::raw(
                                    sprintf(
                                        '"%s" as suffix',
                                        MediaImgFormat::getFormatAcronyms(MediaImgFormat::THUMBNAIL)
                                    )
                                )
                            ])->toArray(),
                            Response::HTTP_OK
                        );
                        break;
                    default:
                        break;
                }
            } catch (\Exception $e) {
                return response([
                    'msg' => trans('error.media.type_size')
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            return response([
                'filename' => $media->getHddFilename(),
            ], Response::HTTP_OK);
        }
        return response([
            null
        ], Response::HTTP_INTERNAL_SERVER_ERROR);

    }

    /**
     * @param \App\Support\Media\UploadedImage $media
     * @return int
     * @throws \Exception
     */
    private function processImage($media)
    {
        $media->move();
        $media->makeThumbnail();
        $this->mediaRepo->image()->cropImageToFormat(
            $media->getUuid(),
            $media->getTargetType(),
            $media->getMediaType(),
            $media->getFileExtension(),
            MediaImgFormat::PAGE
        );
        return $this->mediaRepo->image()->save($media);
    }

    /**
     * @param \App\Support\Providers\User $user
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function crop(UserProvider $user)
    {
        $input = (object)$this->request->only(['uuid', 'height', 'width', 'x', 'y']);

        /**
         * @var \App\Support\Media\SimpleImage $avatarInfo
         */
        $avatarInfo = \Cache::get('temporary_avatars')->pull(substr($input->uuid, 0, 32));
        $avatarInfo->cropAvatar($input);
        $this->mediaRepo->image()->saveAvatar($avatarInfo);

        return response($user->getAvatars($this->user->getKey()), Response::HTTP_OK);

    }


}