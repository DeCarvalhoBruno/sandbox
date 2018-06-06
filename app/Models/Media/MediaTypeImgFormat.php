<?php namespace App\Models\Media;

use App\Support\Media\Image;
use App\Traits\Enumerable;
use Illuminate\Database\Eloquent\Model;

class MediaTypeImgFormat extends Model
{
    use Enumerable;

    const ORIGINAL = 1;
    const THUMBNAIL = 2;
    public $timestamps = false;

    protected $primaryKey = 'media_type_img_format_id';
    protected $fillable = [
        'media_type_img_format_name',
        'media_type_img_format_dimensions',
        'media_type_img_format_acronym'
    ];

    public static function getFormatDimensions($formatID = null)
    {
        switch ($formatID) {
            case static::ORIGINAL:
                return (object)['width' => '0', 'height' => '0'];
            case static::THUMBNAIL:
                return (object)['width' => Image::$thumbnailWidth, 'height' => Image::$thumbnailHeight];
            default:
                return [
                    static::ORIGINAL  => (object)['width' => '0', 'height' => '0'],
                    static::THUMBNAIL => (object)['width' => Image::$thumbnailWidth, 'height' => Image::$thumbnailHeight],
                ];
        }
    }

    public static function getFormatAcronyms($formatID = null)
    {
        switch ($formatID) {
            case static::ORIGINAL:
                return '';
            case static::THUMBNAIL:
                return 'tb';
            default:
                return [
                    static::ORIGINAL  => '',
                    static::THUMBNAIL => 'tb',
                ];
        }
    }

}
