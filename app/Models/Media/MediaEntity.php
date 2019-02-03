<?php namespace App\Models\Media;

use App\Traits\Models\DoesSqlStuff;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class MediaEntity extends Model
{
    use DoesSqlStuff;

    public $timestamps = false;
    protected $table = 'media_entities';
    protected $primaryKey = 'media_entity_id';
    protected $fillable = ['entity_type_id', 'media_category_record_id'];

    /**
     * Sets the media as being used by a specific entity (user, forum thread etc.)
     * We call a stored procedure that sets all other media from that entity to "not used", because this method is called by entities
     * that can only use one media at a time (i.e profile picture in users, logo in owners or websites, etc.)
     *
     * @param $mediaEntityId
     */
    public static function setMediaAsUsed($mediaEntityId)
    {
        $model = new static();
        $model->newQuery()->where($model->getKeyName(), $mediaEntityId)->update(['media_entity_in_use' => true]);
        \DB::unprepared(sprintf('CALL sp_update_media_entity_in_use(%s)', $mediaEntityId));
    }

    public static function buildImages($columns = ['*'], $entityTypeId = null)
    {
        return static::query()->select($columns)
            ->entityType($entityTypeId)->mediaCategoryRecord()
            ->mediaRecord()->mediaType()->mediaDigital();
    }

    /**
     * @link https://laravel.com/docs/5.7/eloquent#local-scopes
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param int $entityTypeId
     * @return \Illuminate\Database\Eloquent\Builder $builder
     */
    public function scopeEntityType(Builder $builder, $entityTypeId = null)
    {
        return $builder->join('entity_types', function ($q) use ($entityTypeId) {
            $q->on('entity_types.entity_type_id', '=', 'media_entities.entity_type_id');
            if (!is_null($entityTypeId)) {
                $q->where('entity_types.entity_type_id', '=', $entityTypeId);
            }
        });
    }

    /**
     * @link https://laravel.com/docs/5.7/eloquent#local-scopes
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param int $mediaCategoryId
     * @return \Illuminate\Database\Eloquent\Builder $builder
     */
    public static function scopeMediaCategoryRecord(Builder $builder, $mediaCategoryId = MediaCategory::MEDIA)
    {
        return $builder->join('media_category_records', function ($q) use ($mediaCategoryId) {
            $q->on('media_entities.media_category_record_id', '=', 'media_category_records.media_category_record_id');
            $q->where('media_category_records.media_category_id', '=', $mediaCategoryId);
        });
    }

    /**
     * @link https://laravel.com/docs/5.7/eloquent#local-scopes
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @return \Illuminate\Database\Eloquent\Builder $builder
     */
    public static function scopeMediaRecord(Builder $builder)
    {
        return $builder->join('media_records',
            'media_category_records.media_record_target_id', '=', 'media_records.media_record_id');
    }

    /**
     * @link https://laravel.com/docs/5.7/eloquent#local-scopes
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @return \Illuminate\Database\Eloquent\Builder $builder
     */
    public static function scopeMediaType(Builder $builder)
    {
        return $builder->join('media_types',
            'media_types.media_type_id', '=', 'media_records.media_type_id');
    }

    /**
     * @link https://laravel.com/docs/5.7/eloquent#local-scopes
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @return \Illuminate\Database\Eloquent\Builder $builder
     */
    public static function scopeMediaDigital(Builder $builder)
    {
        return $builder->join('media_digital',
            'media_digital.media_type_id', '=', 'media_types.media_type_id');
    }
}
