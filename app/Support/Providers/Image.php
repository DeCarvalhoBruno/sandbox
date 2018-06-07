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

/**
 * @method \App\Models\Media\MediaTypeImg createModel(array $attributes = [])
 */
class Image extends Model implements ImageInterface
{

    protected $model = \App\Models\Media\MediaTypeImg::class;

    /**
     * @param $entityId
     * @param \App\Support\Media\Media $media
     * @return \App\Models\Media\MediaEntity
     * @throws \Exception
     */
    public function saveAvatar($entityId, $media)
    {
        $targetEntityTypeId = EntityType::getEntityTypeID($entityId, $media->getTargetName());
        $mediaSystemEntity = $this->createImage($media, $targetEntityTypeId);

        return $mediaSystemEntity;
    }

    /**
     * @param \App\Support\Media\Media $media
     * @param int $entityTypeID
     * @return \App\Models\Media\MediaEntity
     * @throws \Exception
     */
    public function createImage($media, $entityTypeID)
    {
        \DB::beginTransaction();
        //For now the title of the image is the entity's slug, so we have an idea of which is which in mysql
        $mediaType = MediaType::create(['media_type_title' => $media->getTargetName()]);
        $mediaType->save();

        $mediaTypeImg = MediaTypeImg::create([
            'media_type_id' => $mediaType->getKey(),
        ]);
        $mediaTypeImg->save();

        MediaTypeImgFormatType::insert([
            'media_type_img_filename' => $media->getNewName(),
            'media_type_img_original_filename' => $media->getOriginalName(),
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
