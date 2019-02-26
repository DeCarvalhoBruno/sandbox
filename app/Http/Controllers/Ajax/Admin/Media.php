<?php

namespace App\Http\Controllers\Ajax\Admin;

use App\Contracts\Models\Media as MediaInterface;
use App\Http\Requests\Admin\UpdateMedia;
use App\Models\EntityType;
use App\Models\Media\MediaDigital;
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
     * @param \App\Contracts\Models\Media|\App\Support\Providers\Media $mediaRepo
     */
    public function __construct(MediaInterface $mediaRepo)
    {
        parent::__construct();
        $this->mediaRepo = $mediaRepo;
    }

    /**
     * @return \Illuminate\Http\Response
     * @throws \Exception
     * @throws \Throwable
     */
    public function add()
    {
        $input = (object)$this->request->only(['type', 'target', 'media', 'group', 'category', 'file']);

        $file = $this->request->file('file');

        if (!is_null($file)) {
            $fileSize = $file->getSize();
            if ($fileSize < MediaDigital::MEDIA_DIGITAL_MAX_FILESIZE * 1000000) {
                try {
                    //Type is users, forum posts, etc.
                    if (!Entity::isValidName($input->type)) {
                        throw new \UnexpectedValueException(
                            trans('error.media.entity_type', ['type' => $input->type])
                        );
                    }
                    //Media is image, image_avatar, etc.
                    if (!MediaModel::isValidName($input->media)) {
                        throw new \UnexpectedValueException(
                            trans('error.media.media_type', ['type' => $input->media])
                        );
                    }

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
                            return response(trans(
                                'error.media.wrong_type'),
                                Response::HTTP_INTERNAL_SERVER_ERROR
                            );
                    }
                } catch (\Exception $e) {
                    return response([
                        'data' => trans('error.media.type_size'),
                        'error' => $e->getMessage()
                    ], Response::HTTP_INTERNAL_SERVER_ERROR);
                }
                return response([
                    'filename' => $media->getHddFilename(),
                ], Response::HTTP_OK);
            }
            return response([
                trans('js-common.dropzone.file_too_big_laravel', [
                    'filesize' => round($fileSize / 1000000, 2),
                    'maxFilesize' => MediaDigital::MEDIA_DIGITAL_MAX_FILESIZE
                ])
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return response([
            trans('error.media.no_file')
        ], Response::HTTP_INTERNAL_SERVER_ERROR);

    }

    public function edit($uuid)
    {
        $query = $this->mediaRepo->image()->getOne($uuid,
            [
                'media_uuid',
                'media_title',
                'media_alt',
                'created_at',
                'media_description',
                'media_caption',
                'media_id',
                'media_extension'
            ]
        );
        if (is_null($query)) {
            return response(trans('error.http.500.general_retrieval_error'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        $media = $query->toArray();

        $data = $this->mediaRepo->image()->getSiblings($uuid, ['media_uuid', 'entity_type_id'])->get();
        $navData = $data->pluck('media_uuid')->all();
        $fromEntity = $data->pluck('entity_type_id')->first();
        $entityInfo = EntityType::buildQueryFromUnknownEntity($fromEntity);
        $type = null;
        if (!is_null($entityInfo)) {
            $media['media'] = MediaModel::getConstantName($media['media_id']);
            $media['type'] = $type;
            $media['suffix'] = MediaImgFormat::getFormatAcronyms(MediaImgFormat::THUMBNAIL);
        }

        $index = array_search($uuid, $navData);
        $total = count($navData);
        $nav = array(
            'total' => $total,
            'idx' => ($index + 1),
            'first' => isset($navData[0]) ? $navData[0] : null,
            'last' => $navData[($total - 1)],
            'prev' => isset($navData[$index - 1]) ? ($navData[$index - 1] ?? null) : null,
            'next' => isset($navData[$index + 1]) ? ($navData[$index + 1] ?? null) : null
        );
        return [
            'media' => $media,
            'nav' => $nav,
        ];
    }

    /**
     *
     * @param string $uuid
     * @param \App\Http\Requests\Admin\UpdateMedia $request
     * @param \App\Contracts\Models\Media|\App\Support\Providers\Media $mediaRepo
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function update($uuid, UpdateMedia $request, MediaInterface $mediaRepo)
    {
        if (is_hex_uuid_string($uuid)) {
            $mediaRepo->image()->updateOne($uuid, $request->all());
        }
        return response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @param \App\Support\Media\UploadedImage $media
     * @return int
     * @throws \Exception
     * @throws \Throwable
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
            MediaImgFormat::FEATURED
        );
        $this->mediaRepo->image()->cropImageToFormat(
            $media->getUuid(),
            $media->getTargetType(),
            $media->getMediaType(),
            $media->getFileExtension(),
            MediaImgFormat::HD
        );
        return $this->mediaRepo->image()->saveImageDb(
            $media,
            [
                MediaImgFormat::FEATURED,
                MediaImgFormat::HD
            ]
        );
    }

    /**
     * @param \App\Support\Providers\User $user
     * @return \Illuminate\Http\Response
     * @throws \Exception
     * @throws \Throwable
     */
    public function crop(UserProvider $user)
    {
        $input = (object)$this->request->only(['uuid', 'height', 'width', 'x', 'y']);

        /**
         * @var \App\Support\Media\SimpleImage $avatarInfo
         */
        $avatarInfo = \Cache::get('temporary_avatars')->pull(substr($input->uuid, 0, 32));
        try {
            $avatarInfo->cropAvatar($input);
            $this->mediaRepo->image()->saveAvatar($avatarInfo);
        } catch (\Exception $e) {
            return response([
                'data' => trans('error.media.type_size'),
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return response($user->getAvatars($this->user->getKey()), Response::HTTP_OK);
    }


}