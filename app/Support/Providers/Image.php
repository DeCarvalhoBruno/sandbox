<?php namespace App\Support\Providers;

use App\Contracts\Models\Image as ImageInterface;
use App\Contracts\Models\Avatar as AvatarInterface;
use App\Contracts\Image as ImageContract;
use App\Models\EntityType;
use App\Models\Media\MediaCategoryRecord;
use App\Models\Media\MediaDigital;
use App\Models\Media\MediaEntity;
use App\Models\Media\MediaRecord;
use App\Models\Media\MediaType;

/**
 * @method \App\Models\Media\MediaDigital createModel(array $attributes = [])
 */
class Image extends Model implements ImageInterface
{
    /**
     * @var \App\Contracts\Models\Avatar|\App\Support\Providers\Avatar
     */
    private $avatar;
    protected $model = \App\Models\Media\MediaDigital::class;

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
        $this->createImage($image, $targetEntityTypeId);
        return $targetEntityTypeId;
    }

    /**
     * @param \App\Contracts\Image|\App\Support\Media\UploadedAvatar $media
     * @param int $entityTypeID
     * @throws \Exception
     */
    public function createImage($media, $entityTypeID)
    {
        \DB::beginTransaction();
        //For now the title of the image is the entity's slug, so we have an idea of which is which in mysql
        $mediaType = MediaType::create([
            'media_title' => $media->getTargetSlug(),
            'media_uuid' => $media->getUuid(),
            'media_id' => $media->getMediaType()
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


}
