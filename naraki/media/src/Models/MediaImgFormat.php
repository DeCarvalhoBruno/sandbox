<?php namespace Naraki\Media\Models;

use Naraki\Media\Support\ImageProcessor;
use App\Traits\Enumerable;
use Illuminate\Database\Eloquent\Model;

class MediaImgFormat extends Model
{
    use Enumerable;

    const ORIGINAL = 1;
    const THUMBNAIL = 2;
    const FEATURED = 3;
    const HD = 4;
    const FHD = 5;
    public $timestamps = false;

    protected $primaryKey = 'media_img_format_id';
    protected $fillable = [
        'media_img_format_name',
        'media_img_format_width',
        'media_img_format_height'
    ];

    public static function getFormatDimensions($formatID = null)
    {
        switch ($formatID) {
            case static::ORIGINAL:
                return (object)['width' => '0', 'height' => '0'];
            case static::THUMBNAIL:
                return (object)[
                    'width' => ImageProcessor::$thumbnailWidth,
                    'height' => ImageProcessor::$thumbnailHeight
                ];
            case static::FEATURED:
                return (object)['width' => 720, 'height' => 540];
            case static::HD:
                return (object)['width' => 1280, 'height' => 720];
            case static::FHD:
                return (object)['width' => 1920, 'height' => 1080];
            default:
                return [
                    static::ORIGINAL => (object)['width' => 0, 'height' => 0],
                    static::THUMBNAIL => (object)[
                        'width' => ImageProcessor::$thumbnailWidth,
                        'height' => ImageProcessor::$thumbnailHeight
                    ],
                    static::FEATURED => (object)['width' => 500, 'height' => 300],
                    static::HD => (object)['width' => 1280, 'height' => 720],
                    static::FHD => (object)['width' => 1920, 'height' => 1080],
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
            case static::FEATURED:
                return 'ft';
            case static::HD:
                return 'hd';
            case static::FHD:
                return 'hf';
            default:
                return [
                    static::ORIGINAL => '',
                    static::THUMBNAIL => 'tb',
                    static::FEATURED => 'ft',
                    static::HD => 'hd',
                    static::FHD => 'hf',
                ];
        }
    }

}
