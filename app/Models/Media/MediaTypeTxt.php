<?php namespace App\Models\Media;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class MediaTypeTxt extends Model
{

    public static $filenameColumn = 'media_type_txt_filename';
    public static $originalFilenameColumn = 'media_type_txt_original_filename';

    protected $table = 'media_type_txt';
    protected $primaryKey = 'media_type_txt_id';
    protected $fillable = ['media_type_id', 'media_type_txt_filename', 'media_type_txt_original_filename'];

    /**
     * @param $systemEntityID
     * @param int $mediaCategoryID
     * @param null $targetID
     * @param bool $includeNotInUse
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function getFileRelatedInfo(
        $systemEntityID,
        $mediaCategoryID = MediaCategory::FILE,
        $targetID = null,
        $includeNotInUse=false
    ) {
        return static::query()
                     ->mediaType()
                     ->mediaRecord()
                     ->mediaCategoryRecord()
                     ->mediaSystemEntity()
                     ->systemEntityTypeByID($systemEntityID, $mediaCategoryID, $targetID, $includeNotInUse);
    }

    public static function createFile(
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

        $mediaTypeTxt = static::create([
            'media_type_id'                    => $mediaType->getKey(),
            'media_type_txt_filename'          => $filename,
            'media_type_txt_original_filename' => $originalFilename,
        ]);
        $mediaTypeTxt->save();

        $mediaRecord = MediaRecord::create([
            'media_type_id' => $mediaType->getKey(),
            'media_id'      => static::getTypeFromFilename($filename)
        ]);
        $mediaRecord->save();

        $mediaCategoryRecord = MediaCategoryRecord::create([
            'media_category_record_target_id' => $mediaRecord->getKey(),
            'media_category_id'               => $mediaCategoryID
        ]);
        $mediaCategoryRecord->save();

        $mediaSystemEntity = MediaEntity::create([
            'system_entity_type_id'      => $entityTypeID,
            'media_category_record_id'   => $mediaCategoryRecord->getKey(),
            'media_system_entity_in_use' => $inUse
        ]);

        \DB::commit();

        return $mediaSystemEntity;
    }

    public static function createGroupingFile($targetID, $filename, $thumbnailFilename, $originalFilename)
    {
        \DB::beginTransaction();
        $mediaType = MediaType::create(['media_type_thumbnail' => $thumbnailFilename]);
        $mediaType->save();

        $mediaTypeImg = static::create([
            'media_type_id'                    => $mediaType->getKey(),
            'media_type_txt_filename'          => $filename,
            'media_type_txt_original_filename' => $originalFilename
        ]);
        $mediaTypeImg->save();

        $mediaRecord = MediaRecord::create([
            'media_type_id' => $mediaType->getKey(),
            'media_id'      => static::getTypeFromFilename($filename)
        ]);
        $mediaRecord->save();

        $mediaGroupingRecordID = MediaGroupRecord::create([
            'media_grouping_type_id' => $targetID,
            'media_record_id'        => $mediaRecord->getKey()
        ]);

        \DB::commit();

        return $mediaGroupingRecordID;
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
        return $query->join('media_types', 'media_type_txt.media_type_id', '=', 'media_types.media_type_id');
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
        $mediaCategoryID = MediaCategory::FILE,
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

    private static function getTypeFromFilename($filename)
    {
        $fileExtension = strtolower(substr($filename, strrpos($filename, '.') - 1));
        switch ($fileExtension) {
            case "jpg":
            case "jpeg":
            case "png":
            case "gif":
            case "tif":
            case "tiff":
            case "psd":
            case "bmp":
            case "ico":
                $type = Media::DIGITAL_IMAGE;
                break;
            default:
                $type = Media::DIGITAL_TEXT;
                break;
        }

        return $type;
    }
}
