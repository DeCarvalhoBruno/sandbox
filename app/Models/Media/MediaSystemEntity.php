<?php namespace App\Models\Media;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class MediaSystemEntity extends Model
{

    public $timestamps = false;
    protected $table = 'media_system_entities';
    protected $primaryKey = 'media_system_entity_id';
    protected $fillable = ['system_entity_type_id', 'media_category_record_id', 'media_system_entity_in_use'];

    /**
     * Sets the media as being used by a specific entity (website, user, etc.)
     * We call a stored procedure that sets all other media from that entity to "not used", because this method is called by entities
     * that can only use one media at a time (i.e profile picture in users, logo in owners or websites, etc.)
     *
     * @param $mediaSystemEntityID
     */
    public static function setMediaAsUsed($mediaSystemEntityID){
        $model = new static();
        $model->newQuery()->where($model->getKeyName(),$mediaSystemEntityID)->update(['media_system_entity_in_use'=>true]);
        \DB::unprepared(sprintf('CALL sp_update_media_system_entity_in_use(%s)',$mediaSystemEntityID));
    }

    /**
     * @param int $mediaSystemEntityID
     *
     * @return \App\Models\Media\MediaSystemEntity
     */
    public static function getImageInfo($mediaSystemEntityID)
    {
        return static::getImage($mediaSystemEntityID)->select(['media_type_img.*','media_types.*'])->first();
    }

    /**
     * @param int $mediaSystemEntityID
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function getImage($mediaSystemEntityID){
        $self = new static();
        return $self->newQuery()
                    ->systemEntityType()
                    ->mediaCategoryRecord()
                    ->mediaRecord()
                    ->mediaType()
                    ->mediaTypeImg()
                    ->mediaFormatType()
                    ->where($self->getKeyName(), $mediaSystemEntityID);
    }

    /**
     * @param int $mediaSystemEntityID
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function getFile($mediaSystemEntityID){
        $self = new static();
        return $self->newQuery()
                    ->systemEntityType()
                    ->mediaCategoryRecord()
                    ->mediaRecord()
                    ->mediaType()
                    ->mediaTypeTxt()
                    ->where($self->getKeyName(), $mediaSystemEntityID);
    }

    /**
     * @link https://laravel.com/docs/5.2/eloquent#query-scopes
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder $query
     */
    public function scopeSystemEntityType(Builder $query)
    {
        return $query->join('system_entity_types', 'media_system_entities.system_entity_type_id', '=',
            'system_entity_types.system_entity_type_id');
    }

    /**
     * @link https://laravel.com/docs/5.2/eloquent#query-scopes
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder $query
     */
    public function scopeMediaCategoryRecord(Builder $query)
    {
        return $query->join('media_category_records', 'media_system_entities.media_category_record_id', '=',
            'media_category_records.media_category_record_id');
    }

    /**
     * @link https://laravel.com/docs/5.2/eloquent#query-scopes
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder $query
     */
    public function scopeMediaRecord(Builder $query)
    {
        return $query->join('media_records', 'media_records.media_record_id', '=',
            'media_category_records.media_category_record_target_id');
    }

    /**
     * @link https://laravel.com/docs/5.2/eloquent#query-scopes
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder $query
     */
    public function scopeMediaType(Builder $query)
    {
        return $query->join('media_types', 'media_records.media_type_id', '=', 'media_types.media_type_id');
    }

    /**
     * @link https://laravel.com/docs/5.2/eloquent#query-scopes
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder $query
     */
    public function scopeMediaTypeImg(Builder $query)
    {
        return $query->join('media_type_img', 'media_types.media_type_id', '=', 'media_type_img.media_type_id');
    }

    /**
     * @link https://laravel.com/docs/5.2/eloquent#query-scopes
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder $query
     */
    public function scopeMediaFormatType(Builder $query)
    {
        return $query->join('media_type_img_format_types', 'media_type_img.media_type_img_id', '=',
            'media_type_img_format_types.media_type_img_id')
                     ->where('media_type_img_format_types.media_type_img_format_id', MediaTypeImgFormat::ORIGINAL);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder $query
     */
    public function scopeMediaTypeTxt(Builder $query)
    {
        return $query->join('media_type_txt', 'media_types.media_type_id', '=', 'media_type_txt.media_type_id');
    }

}
