<?php namespace App\Support\Providers;

use App\Contracts\Models\Image as ImageInterface;
use App\Contracts\Models\Avatar as AvatarInterface;
use App\Contracts\Image as ImageContract;
use App\Models\Entity;
use App\Models\EntityType;
use App\Models\Media\MediaCategoryRecord;
use App\Models\Media\MediaDigital;
use App\Models\Media\MediaEntity;
use App\Models\Media\MediaImgFormat;
use App\Models\Media\MediaRecord;
use App\Models\Media\MediaType;
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

    public function getOne($uuid, $columns = ['*'])
    {
        return $this->createModel()->newQuery()
            ->select($columns)
            ->mediaType()->where('media_uuid', '=', $uuid)->first();

    }

    /**
     * @param \App\Contracts\Image $image
     * @return int
     * @throws \Exception
     */
    public function saveAvatar(ImageContract $image)
    {
        $targetEntityTypeId = $this->save($image);
        $this->avatar->setAsUsed($image->getUuid());
        return $targetEntityTypeId;
    }

    private function getTargetEntity(ImageContract $image)
    {
        return EntityType::getEntityTypeID($image->getTargetType(), $image->getTargetSlug());
    }

    /**
     * @param \App\Contracts\Image $image
     * @return array|int
     * @throws \Exception
     */
    public function save(ImageContract $image)
    {
        $targetEntityTypeId = $this->getTargetEntity($image);
        $this->createImage($image, $targetEntityTypeId, false);
        return $targetEntityTypeId;
    }

    /**
     * @param \App\Contracts\Image|\App\Support\Media\UploadedAvatar $media
     * @param int $entityTypeID
     * @param bool $setAsUsed
     * @throws \Exception
     */
    public function createImage($media, $entityTypeID, $setAsUsed = true)
    {
        \DB::beginTransaction();
        //For now the title of the image is the entity's slug, so we have an idea of which is which in mysql
        $mediaType = MediaType::create([
            'media_title' => $media->getTargetSlug(),
            'media_uuid' => $media->getUuid(),
            'media_id' => $media->getMediaType(),
            'media_in_use' => $setAsUsed
        ]);

        MediaDigital::create([
            'media_type_id' => $mediaType->getKey(),
            'media_extension' => $media->getFileExtension(),
            'media_filename' => $media->getFilename(),
            'media_thumbnail' => $media->getThumbnailFilename()
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

        \DB::commit();
    }

    /**
     * @param int $entityTypeId
     * @param array $columns
     * @return \App\Models\Media\MediaEntity
     */
    public function getImages($entityTypeId, $columns = ['*'])
    {
        return MediaEntity::buildImages($columns, $entityTypeId)->get()->toArray();
    }

    public function getImagesFromSlug(
        $slug,
        $columns = [
            'media_uuid as uuid',
            'media_in_use as used',
            'media_extension as ext'
        ]
    ) {
        return $this->getImages(EntityType::getEntityTypeID(Entity::BLOG_POSTS, $slug), $columns);
    }

    public static function setAsUsed($uuid)
    {
        if (is_hex_uuid_string($uuid)) {
            return \DB::unprepared(sprintf('CALL sp_update_media_type_in_use("%s")', $uuid));
        }
        throw new \UnexpectedValueException('uuid is not valid');
    }

    public function delete($uuid, $entityId, $imageType)
    {
        if (is_hex_uuid_string($uuid)) {
            /** @var \App\Models\Media\MediaType $media */
            $media = MediaType::query()->select([
                'media_types.media_type_id',
                'media_uuid',
                'media_extension'
            ])->where('media_uuid', '=', $uuid)
                ->mediaDigital()
                ->first();
            if (!is_null($media)) {
                $this->deleteFiles(
                    $entityId,
                    $imageType,
                    $uuid,
                    $media->getAttribute('media_extension')
                );
                return $media->delete();
            }
            throw new \UnexpectedValueException('Media was not found');
        }
        throw new \UnexpectedValueException('uuid is not valid');
    }

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
}
