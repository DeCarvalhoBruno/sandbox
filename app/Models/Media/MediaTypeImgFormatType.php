<?php namespace App\Models\Media;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class MediaTypeImgFormatType extends Model
{
    public static $filenameColumn = 'media_type_img_filename';
    protected $primaryKey = 'media_type_img_format_type_id';
    protected $fillable = ['media_type_img_original_filename','media_type_img_filename', 'media_type_img_id','media_type_img_format_id'];

    /**
     * @see \App\Models\MediaTypeImgFormat
     *
     * @param int $imgID
     * @param int $formatID
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function getFormat($imgID,$formatID){
        return static::query()->img()->where('media_type_img.media_type_img_id',$imgID)->where('media_type_img_format_id',$formatID);
    }

    /**
     * @link https://laravel.com/docs/5.2/eloquent#query-scopes
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder $query
     */
    public function scopeImg(Builder $query)
    {
        return $query->join('media_type_img', 'media_type_img_format_types.media_type_img_id', '=', 'media_type_img.media_type_img_id');
    }

}
