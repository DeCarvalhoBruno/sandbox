<?php namespace App\Support\Providers;

use App\Contracts\Models\Image as ImageInterface;
use App\Contracts\Models\Avatar as AvatarInterface;
use App\Contracts\Image as ImageContract;
use App\Models\Entity;
use App\Models\EntityType;
use App\Models\Media\MediaCategoryRecord;
use App\Models\Media\MediaDigital;
use App\Models\Media\MediaEntity;
use App\Models\Media\MediaImg;
use App\Models\Media\MediaImgFormat;
use App\Models\Media\MediaImgFormatType;
use App\Models\Media\MediaRecord;
use App\Models\Media\MediaType;
use App\Models\Views\EntitiesWithMedia;
use App\Support\Media\ImageProcessor;

/**
 * @method \App\Models\Media\MediaDigital createModel(array $attributes = [])
 */
class Image extends Model implements ImageInterface
{
    protected $model = \App\Models\Media\MediaDigital::class;
    /**
     * @var \App\Contracts\Models\Avatar|\App\Support\Providers\Avatar
     */
    private $avatar;

    public function __construct(AvatarInterface $ai, $model = null)
    {
        parent::__construct($model);
        $this->avatar = $ai;
    }

    /**
     * @return \App\Contracts\Models\Avatar|\App\Support\Providers\Avatar
     */
    public function avatar()
    {
        return $this->avatar;
    }

    /**
     * @param string $uuid
     * @param array $columns
     * @return mixed
     */
    public function getOne($uuid, $columns = ['*'])
    {
        return $this->createModel()->newQuery()
            ->select($columns)
            ->mediaType()->where('media_uuid', '=', $uuid)->first();
    }

    public function updateOne($uuid, $data)
    {
        $mediaTypeModel = new MediaType();

        $media = $mediaTypeModel->newQuery()->select(['media_type_id'])
            ->where('media_uuid', '=', $uuid)->first();
        if (is_null($media)) {
            return;
        }
        $fillables = $this->filterFillables($data, $mediaTypeModel);
        if (!empty($fillables)) {
            $media->update($fillables);
        }

        $model = $this->createModel();
        $fillables = $this->filterFillables($data, $model);
        if (!empty($fillables)) {
            $model->newQuery()->where('media_type_id', '=', $media->getKey())
                ->update($fillables);
        }
    }

    /**
     * @param \App\Contracts\Image $image
     * @return int
     * @throws \Exception
     * @throws \Throwable
     */
    public function saveAvatar(ImageContract $image)
    {
        $targetEntityTypeId = $this->saveImageDb($image);
        if (env('APP_ENV') !== 'testing') {
            $this->setAsUsed($image->getUuid());
        }
        return $targetEntityTypeId;
    }

    private function getTargetEntity(ImageContract $image)
    {
        return EntityType::getEntityTypeID($image->getTargetType(), $image->getTargetSlug());
    }

    /**
     * @param \App\Contracts\Image $image
     * @param array $formats
     * @return array|int
     * @throws \Throwable
     */
    public function saveImageDb(ImageContract $image, $formats = null)
    {
        $targetEntityTypeId = $this->getTargetEntity($image);
        if (is_null($targetEntityTypeId)) {
            throw new \UnexpectedValueException(trans('error.media.entity_not_found'));
        }
        $this->createImage($image, $targetEntityTypeId, false, $formats);
        return $targetEntityTypeId;
    }

    /**
     * @param \App\Contracts\Image|\App\Support\Media\UploadedAvatar $media
     * @param int $entityTypeID
     * @param bool $setAsUsed
     * @param array $formats
     * @throws \Throwable
     */
    public function createImage($media, $entityTypeID, $setAsUsed = true, $formats = null)
    {
        \DB::transaction(function () use ($media, $entityTypeID, $setAsUsed, $formats) {
            //For now the title of the image is the entity's slug, so we have an idea of which is which in mysql
            $mediaType = MediaType::create([
                'media_title' => $media->getFilename(),
                'media_uuid' => $media->getUuid(),
                'media_id' => $media->getMediaType(),
                'media_in_use' => $setAsUsed
            ]);

            $mediaDigital = MediaDigital::create([
                'media_type_id' => $mediaType->getKey(),
                'media_extension' => $media->getFileExtension(),
                'media_filename' => $media->getFilename(),
            ]);

            $mediaRecord = MediaRecord::create([
                'media_type_id' => $mediaType->getKey(),
            ]);

            $mediaCategoryRecord = MediaCategoryRecord::create([
                'media_record_target_id' => $mediaRecord->getKey(),
            ]);

            MediaEntity::create([
                'entity_type_id' => $entityTypeID,
                'media_category_record_id' => $mediaCategoryRecord->getKey(),
            ]);

            MediaImg::create([
                'media_digital_id' => $mediaDigital->getKey()
            ]);

            if (!is_null($formats)) {
                foreach ($formats as $format) {
                    MediaImgFormatType::create([
                        'media_digital_id' => $mediaDigital->getKey(),
                        'media_img_format_id' => $format
                    ]);
                }
            }
        });
    }

    /**
     * @param int|array $entityTypeId
     * @param array $columns
     * @return \App\Models\Media\MediaEntity
     */
    public function getImages($entityTypeId, $columns = null)
    {
        if (is_null($columns)) {
            $columns = $this->getDefaultImageColumns();
        }
        return MediaEntity::buildImages($entityTypeId, $columns)->get();
    }

    /**
     * @param string $slug
     * @param int $entityId
     * @param array $columns
     * @return \App\Models\Media\MediaEntity
     */
    public function getImagesFromSlug($slug, $entityId = Entity::BLOG_POSTS, $columns = ['*'])
    {
        if ($columns[0] == '*') {
            $columns = [
                'media_uuid as uuid',
                'media_in_use as used',
                'media_extension as ext',
                'media_title as title',
                \DB::raw(
                    sprintf(
                        '"%s" as suffix',
                        MediaImgFormat::getFormatAcronyms(MediaImgFormat::THUMBNAIL)
                    )
                )
            ];
        }
        return $this->getImages(EntityType::getEntityTypeID($entityId, $slug), $columns);
    }

    /**
     * @param string $uuid
     * @return bool
     */
    public static function setAsUsed($uuid)
    {
        if (is_hex_uuid_string($uuid)) {
            return \DB::unprepared(sprintf('CALL sp_update_media_type_in_use("%s")', $uuid));
        }
        throw new \UnexpectedValueException('uuid is not valid');
    }

    /**
     * @param string|array $uuid
     * @param int $entityId
     * @param int $mediaType
     * @throws \Exception
     */
    public function delete($uuid, $entityId, $mediaType = \App\Models\Media\Media::IMAGE)
    {
        /** @var \App\Models\Media\MediaType $media */
        $builder = MediaType::query()
            ->select([
                'media_types.media_type_id',
                'media_uuid',
                'media_extension'
            ])->mediaDigital();
        $media = null;
        if (is_string($uuid)) {
            if (is_hex_uuid_string($uuid)) {
                $media = $builder->where('media_uuid', '=', $uuid)
                    ->get();
            }
        } elseif (is_array($uuid)) {
            $media = $builder->whereIn('media_uuid', $uuid)
                ->get();
        } else {
            throw new \UnexpectedValueException('uuid is not valid');
        }
        if (!is_null($media)) {
            foreach ($media as $record) {
                $this->deleteFiles(
                    $entityId,
                    $mediaType,
                    $record->getAttribute('media_uuid'),
                    $record->getAttribute('media_extension')
                );
                $record->delete();
            }
        }
    }

    /**
     * @param int $entityId
     * @param int $imageType
     * @param string $uuid
     * @param string $fileExtension
     */
    public function deleteFiles($entityId, $imageType, $uuid, $fileExtension)
    {
        $formats = MediaImgFormat::getFormatAcronyms();
        foreach ($formats as $format) {
            $suffix = '';
            if (!empty($format)) {
                $suffix .= sprintf('_%s', $format);
            }
            @\File::delete(
                media_entity_root_path(
                    $entityId,
                    $imageType,
                    sprintf('%s%s.%s', $uuid, $suffix, $fileExtension)
                )
            );
        }
    }

    /**
     * @param string $uuid
     * @param int $entityId
     * @param int $imageType
     * @param string $fileExtension
     * @param int $format
     */
    public function cropImageToFormat($uuid, $entityId, $imageType, $fileExtension, $format = MediaImgFormat::THUMBNAIL)
    {
        ImageProcessor::saveImg(
            ImageProcessor::makeCroppedImage(
                media_entity_root_path(
                    $entityId,
                    $imageType,
                    ImageProcessor::makeFormatFilename(
                        $uuid,
                        $fileExtension
                    )
                ),
                $format
            ),
            media_entity_root_path(
                $entityId,
                $imageType,
                ImageProcessor::makeFormatFilename($uuid, $fileExtension, $format)
            )
        );
    }

    /**
     *  Get images that are attached to the same entity, i.e. all images that are used in a blog post.
     *
     * @param $uuid
     * @param array $columns
     * @return \App\Models\Views\EntitiesWithMedia
     */
    public function getSiblings($uuid, $columns = ['*'])
    {
        return EntitiesWithMedia::getSiblings($uuid, $columns);

    }

    public function getDefaultImageColumns()
    {
        return [
            'media_uuid as uuid',
            'media_in_use as used',
            'media_extension as ext',
            'media_title as title',
            \DB::raw(
                sprintf(
                    '"%s" as suffix',
                    MediaImgFormat::getFormatAcronyms(MediaImgFormat::THUMBNAIL)
                )
            ),
        ];
    }
}
