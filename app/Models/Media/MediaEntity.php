<?php namespace App\Models\Media;

use App\Traits\Models\DoesSqlStuff;
use App\Traits\Presentable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Support\Presenters\MediaEntity as MediaEntityPresenter;

class MediaEntity extends Model
{
    use DoesSqlStuff, Presentable;

    public $timestamps = false;
    protected $presenter = MediaEntityPresenter::class;
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

    /**
     * @param int $entityTypeId
     * @param array $columns
     * @param array $mediaImgTypes
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function buildImages($entityTypeId = null, $columns = ['*'], $mediaImgTypes = null)
    {
        return static::query()->select($columns)
            ->scopes([
                'entityType' => $entityTypeId,
                'mediaCategoryRecord',
                'mediaRecord',
                'mediaType',
                'mediaDigital'
            ]);
    }

    /**
     * @param int $entityId
     * @param int $mediaId
     * @param int $mediaImgFormatId
     * @return string
     */
    public function asset(int $entityId, int $mediaId, int $mediaImgFormatId): string
    {
        return media_entity_path(
            $entityId,
            $mediaId,
            sprintf(
                '%s_%s.%s',
                $this->getAttribute('uuid'),
                MediaImgFormat::getFormatAcronyms($mediaImgFormatId),
                $this->getAttribute('ext')
            )
        );
    }

    /**
     * @link https://laravel.com/docs/5.7/eloquent#local-scopes
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param int|array $entityTypeId
     * @return \Illuminate\Database\Eloquent\Builder $builder
     */
    public function scopeEntityType(Builder $builder, $entityTypeId = null)
    {
        return $builder->join('entity_types', function ($q) use ($entityTypeId) {
            $q->on('entity_types.entity_type_id', '=', 'media_entities.entity_type_id');
            if (!is_null($entityTypeId)) {
                if (!is_array($entityTypeId)) {
                    $q->where('entity_types.entity_type_id', '=', $entityTypeId);
                } else {
                    $q->whereIn('entity_types.entity_type_id', $entityTypeId);
                }
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

    /**
     * @link https://laravel.com/docs/5.7/eloquent#local-scopes
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @return \Illuminate\Database\Eloquent\Builder $builder
     */
    public static function scopeMediaEntities(Builder $builder)
    {
        return $builder->join('media_entities',
            'media_entities.entity_type_id',
            '=',
            'entity_types.entity_type_id'
        );
    }

    /**
     * @link https://laravel.com/docs/5.7/eloquent#local-scopes
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @return \Illuminate\Database\Eloquent\Builder $builder
     */
    public static function scopeImage($builder)
    {
        return self::scopeMediaDigital(
            self::scopeMediaType(
                self::scopeMediaRecord(
                    self::scopeMediaCategoryRecord(
                        self::scopeMediaEntities($builder))))
        );
    }
}
