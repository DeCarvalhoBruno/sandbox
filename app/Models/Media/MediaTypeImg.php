<?php namespace App\Models\Media;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class MediaTypeImg extends Model
{
    protected $table = 'media_type_img';
    protected $primaryKey = 'media_type_img_id';
    protected $fillable = ['media_type_id'];

    /**
     * @param $systemEntityID
     * @param int $mediaCategoryID
     * @param null $targetID
     * @param int $formatID
     * @param bool $includeNotInUse
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function getImageRelatedInfo(
        $systemEntityID,
        $mediaCategoryID = MediaCategory::IMAGE,
        $targetID = null,
        $formatID = MediaTypeImgFormat::ORIGINAL,
        $includeNotInUse = false
    ) {
        $query = static::query()
                     ->mediaType()
                     ->mediaRecord()
                     ->mediaCategoryRecord()
                     ->mediaSystemEntity()
                     ->systemEntityTypeByID($systemEntityID, $mediaCategoryID, $targetID, $includeNotInUse);
        if($formatID!==MediaTypeImgFormat::THUMBNAIL){
            $query->mediaFormatType($formatID);
        }
        return $query;
    }

    /**
     * @param $systemEntityID
     * @param int $mediaCategoryID
     * @param null $targetID
     * @param int $formatID
     * @param bool $includeNotInUse
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function getImageInfo(
        $systemEntityID,
        $mediaCategoryID = MediaCategory::IMAGE,
        $targetID = null,
        $formatID = MediaTypeImgFormat::ORIGINAL,
        $includeNotInUse = false
    ) {
        return static::getImageRelatedInfo($systemEntityID, $mediaCategoryID, $targetID, $formatID, $includeNotInUse)
                     ->select(['media_type_img.*', 'media_types.*', 'media_type_img_format_types.*']);
    }

    public static function getImageID(
        $systemEntityID,
        $mediaCategoryID = MediaCategory::IMAGE,
        $targetID,
        $formatID = MediaTypeImgFormat::ORIGINAL,
        $includeNotInUse = false
    ) {
        return static::getImageRelatedInfo($systemEntityID, $mediaCategoryID, $targetID, $formatID, $includeNotInUse)
                     ->select('media_type_img.media_type_img_id')->value('media_type_img_id');
    }

    public static function getImage(Builder $query = null)
    {
        if (is_null($query)) {
            $query = static::query();
        }

        return $query->mediaFormatType()->mediaType()
                     ->mediaRecord()
                     ->mediaCategoryRecord()
                     ->mediaSystemEntity()
                     ->systemEntityType();
    }

    /**
     * @link https://laravel.com/docs/5.2/eloquent#query-scopes
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $formatID
     *
     * @return \Illuminate\Database\Eloquent\Builder $query
     */
    public function scopeMediaFormatType(Builder $query, $formatID = MediaTypeImgFormat::ORIGINAL)
    {
        $query->join('media_type_img_format_types', 'media_type_img.media_type_img_id', '=',
            'media_type_img_format_types.media_type_img_id');
        if (is_array($formatID)) {
            $query->whereIn('media_type_img_format_types.media_type_img_format_id', $formatID);
        //Every media has a thumbnail, so we don't create a type record for thumbnails, the thumbnail is stored in media_types
        } else if($formatID!==MediaTypeImgFormat::THUMBNAIL){
            $query->where('media_type_img_format_types.media_type_img_format_id', $formatID);
        }
        return $query;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @link https://laravel.com/docs/5.2/eloquent#query-scopes
     *
     * @return \Illuminate\Database\Eloquent\Builder $query
     */
    public function scopeMediaType(Builder $query)
    {
        return $query->join('media_types', 'media_type_img.media_type_id', '=', 'media_types.media_type_id');
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @link https://laravel.com/docs/5.2/eloquent#query-scopes
     *
     * @return \Illuminate\Database\Eloquent\Builder $query
     */
    public function scopeMediaRecord(Builder $query)
    {
        return $query->join('media_records', 'media_types.media_type_id', '=', 'media_records.media_type_id');
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @link https://laravel.com/docs/5.2/eloquent#query-scopes
     *
     * @return \Illuminate\Database\Eloquent\Builder $query
     */
    public function scopeMediaCategoryRecord(Builder $query)
    {
        return $query->join('media_category_records', 'media_records.media_record_id', '=',
            'media_category_records.media_category_record_target_id');
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @link https://laravel.com/docs/5.2/eloquent#query-scopes
     *
     * @return \Illuminate\Database\Eloquent\Builder $query
     */
    public function scopeMediaSystemEntity(Builder $query)
    {
        return $query->join('media_system_entities', 'media_system_entities.media_category_record_id', '=',
            'media_category_records.media_category_record_id');
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $systemEntityID
     * @param int $mediaCategoryID
     * @param int|array $targetID
     * @param boolean $includeNotInUse
     *
     * @link https://laravel.com/docs/5.2/eloquent#query-scopes
     *
     * @return \Illuminate\Database\Eloquent\Builder $query
     */
    public function scopeSystemEntityTypeByID(
        Builder $query,
        $systemEntityID,
        $mediaCategoryID = MediaCategory::IMAGE,
        $targetID = null,
        $includeNotInUse = false
    ) {
        $result = $query->join(
            'system_entity_types',
            'system_entity_types.system_entity_type_id', '=', 'media_system_entities.system_entity_type_id')
                        ->where('system_entity_id', $systemEntityID)
                        ->where('media_category_id', $mediaCategoryID);
        if ( ! $includeNotInUse) {
            $query->where('media_system_entity_in_use', true);
        }

        if ( ! is_null($targetID)) {
            if (is_array($targetID)) {
                $result->whereIn('system_entity_type_target_id', $targetID);
            } else {
                $result->where('system_entity_type_target_id', $targetID);
            }
        }

        return $result;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @link https://laravel.com/docs/5.2/eloquent#query-scopes
     *
     * @return \Illuminate\Database\Eloquent\Builder $query
     */
    public function scopeSystemEntityType(Builder $query)
    {
        return $query->join(
            'system_entity_types',
            'system_entity_types.system_entity_type_id', '=', 'media_system_entities.system_entity_type_id'
        );
    }

    public static function createImage(
        $entityTypeID,
        $mediaCategoryID,
        $filename,
        $thumbnailFilename,
        $originalFilename,
        $inUse = false
    ) {
        \DB::beginTransaction();
        $mediaType = MediaType::create(['media_type_thumbnail' => $thumbnailFilename]);
        $mediaType->save();

        $mediaTypeImg = static::create([
            'media_type_id' => $mediaType->getKey(),
        ]);
        $mediaTypeImg->save();

        MediaTypeImgFormatType::insert([
            'media_type_img_filename'          => $filename,
            'media_type_img_original_filename' => $originalFilename,
            'media_type_img_id'                => $mediaTypeImg->getKey(),
            'media_type_img_format_id'         => MediaTypeImgFormat::ORIGINAL
        ]);

        $mediaRecord = MediaRecord::create([
            'media_type_id' => $mediaType->getKey(),
            'media_id'      => Media::DIGITAL_IMAGE
        ]);
        $mediaRecord->save();

        $mediaCategoryRecord = MediaCategoryRecord::create([
            'media_category_record_target_id' => $mediaRecord->getKey(),
            'media_category_id'               => $mediaCategoryID
        ]);
        $mediaCategoryRecord->save();

        $mediaSystemEntity = MediaSystemEntity::create([
            'system_entity_type_id'      => $entityTypeID,
            'media_category_record_id'   => $mediaCategoryRecord->getKey(),
            'media_system_entity_in_use' => $inUse
        ]);

        \DB::commit();

        return $mediaSystemEntity;
    }

    public static function createGroupingImage($targetID, $filename, $thumbnailFilename, $originalFilename)
    {
        \DB::beginTransaction();
        $mediaType = MediaType::create(['media_type_thumbnail' => $thumbnailFilename]);
        $mediaType->save();

        $mediaTypeImg = static::create([
            'media_type_id' => $mediaType->getKey(),
        ]);
        $mediaTypeImg->save();

        MediaTypeImgFormatType::insert([
            'media_type_img_filename'          => $filename,
            'media_type_img_original_filename' => $originalFilename,
            'media_type_img_id'                => $mediaTypeImg->getKey(),
            'media_type_img_format_id'         => MediaTypeImgFormat::ORIGINAL
        ]);

        $mediaRecord = MediaRecord::create([
            'media_type_id' => $mediaType->getKey(),
            'media_id'      => Media::DIGITAL_IMAGE
        ]);
        $mediaRecord->save();

        $mediaGroupingRecordID = MediaGroupRecord::create([
            'media_grouping_type_id' => $targetID,
            'media_record_id'        => $mediaRecord->getKey()
        ]);

        \DB::commit();

        return $mediaGroupingRecordID;
    }

    public function getFilename()
    {
        return $this->getAttribute(MediaTypeImgFormatType::$filenameColumn);
    }

    public function getThumbnailFilename()
    {
        return $this->getAttribute(MediaType::$thumbnailColumn);
    }

    public function getPath()
    {
        return media_entity_path($this->getAttribute('system_entity_id'), $this->getAttribute('media_category_id'),
            $this->getAttribute('media_type_img_filename'));
    }

    public function getThumbnailPath()
    {
        return media_entity_path($this->getAttribute('system_entity_id'), $this->getAttribute('media_category_id'),
            $this->getAttribute('media_type_thumbnail'));
    }


}
