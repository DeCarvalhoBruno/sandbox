<?php namespace App\Support\Providers;

use App\Contracts\Image as ImageContract;
use App\Contracts\Models\Image as ImageInterface;
use App\Models\EntityType;
use App\Models\Media\MediaCategory;
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

    protected $model = \App\Models\Media\MediaDigital::class;

    /**
     * @param $entityId
     * @param \App\Contracts\Image $image
     * @return \App\Models\Media\MediaEntity
     * @throws \Exception
     */
    public function saveAvatar($entityId, ImageContract $image)
    {
        $targetEntityTypeId = EntityType::getEntityTypeID($entityId, $image->getTargetSlug());
        if(!is_int($targetEntityTypeId)){
            throw new \UnexpectedValueException('Entity could not be found for image saving');
        }
        $mediaSystemEntity = $this->createImage($image, $targetEntityTypeId);

        return $mediaSystemEntity;
    }

    /**
     * @param \App\Support\Media\UploadedImage $media
     * @param int $entityTypeID
     * @return \App\Models\Media\MediaEntity
     * @throws \Exception
     */
    public function createImage($media, $entityTypeID)
    {
        \DB::beginTransaction();
        //For now the title of the image is the entity's slug, so we have an idea of which is which in mysql
        $mediaType = MediaType::create([
            'media_title' => $media->getTargetSlug(),
            'media_uuid' => $media->getUuid(),
        ]);
        $mediaType->save();

        $mediaImg = MediaDigital::create([
            'media_type_id' => $mediaType->getKey(),
            'media_extension' => $media->getFileExtension(),
            'media_filename' => $media->getFilename(),
        ]);
        $mediaImg->save();

        $mediaRecord = MediaRecord::create([
            'media_type_id' => $mediaType->getKey(),
            'media_id' => \App\Models\Media\Media::IMAGE_AVATAR
        ]);
        $mediaRecord->save();

        $mediaCategoryRecord = MediaCategoryRecord::create([
            'media_record_target_id' => $mediaRecord->getKey(),
        ]);
        $mediaCategoryRecord->save();

        $mediaSystemEntity = MediaEntity::create([
            'entity_type_id' => $entityTypeID,
            'media_category_record_id' => $mediaCategoryRecord->getKey(),
        ]);

        \DB::commit();

        return $mediaSystemEntity;

    }


}
