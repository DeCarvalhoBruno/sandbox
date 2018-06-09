<?php namespace App\Support\Providers;

use App\Contracts\Models\Image as ImageInterface;
use App\Models\EntityType;
use App\Models\Media\MediaCategory;
use App\Models\Media\MediaCategoryRecord;
use App\Models\Media\MediaEntity;
use App\Models\Media\MediaRecord;
use App\Models\Media\MediaType;
use App\Models\Media\MediaTypeImg;
use App\Models\Media\MediaTypeImgFormat;
use App\Models\Media\MediaTypeImgFormatType;
use Ramsey\Uuid\Uuid;
use App\Contracts\Image as ImageContract;

/**
 * @method \App\Models\Media\MediaTypeImg createModel(array $attributes = [])
 */
class Image extends Model implements ImageInterface
{

    protected $model = \App\Models\Media\MediaTypeImg::class;

    /**
     * @param $entityId
     * @param \App\Contracts\Image $image
     * @return \App\Models\Media\MediaEntity
     * @throws \Exception
     */
    public function saveAvatar($entityId, ImageContract $image)
    {
        $targetEntityTypeId = EntityType::getEntityTypeID($entityId, $image->getTargetSlugName());
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
        $mediaType = MediaType::create(['media_type_title' => $media->getTargetSlugName()]);
        $mediaType->save();

        $mediaTypeImg = MediaTypeImg::create([
            'media_type_id' => $mediaType->getKey(),
        ]);
        $mediaTypeImg->save();

        MediaTypeImgFormatType::insert([
            'media_type_img_filename' => $media->getNewFilename(),
            'media_type_img_original_filename' => $media->getOriginalFilename(),
            'media_type_img_id' => $mediaTypeImg->getKey(),
            'media_type_img_format_id' => MediaTypeImgFormat::ORIGINAL
        ]);

        $mediaRecord = MediaRecord::create([
            'media_type_id' => $mediaType->getKey(),
            'media_id' => \App\Models\Media\Media::IMAGE_AVATAR
        ]);
        $mediaRecord->save();

        $mediaCategoryRecord = MediaCategoryRecord::create([
            'media_category_record_target_id' => $mediaRecord->getKey(),
            'media_category_id' => MediaCategory::MEDIA
        ]);
        $mediaCategoryRecord->save();

        $mediaSystemEntity = MediaEntity::create([
            'entity_type_id' => $entityTypeID,
            'media_category_record_id' => $mediaCategoryRecord->getKey(),
            'media_entity_slug' => Uuid::uuid4()->getHex(),
            'media_entity_in_use' => true
        ]);

        \DB::commit();

        return $mediaSystemEntity;

    }


}
