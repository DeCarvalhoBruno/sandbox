<?php namespace App\Models\Media;

use App\Support\Media\ImageProcessor;
use App\Traits\Enumerable;
use Illuminate\Database\Eloquent\Model;

class MediaImgFormat extends Model
{
    use Enumerable;

    const ORIGINAL = 1;
    const THUMBNAIL = 2;
    const FEATURED = 3;
    const PAGE = 4;
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
                return (object)['width' => 500, 'height' => 300];
            case static::PAGE:
                return (object)['width' => 720, 'height' => 540];
            default:
                return [
                    static::ORIGINAL => (object)['width' => '0', 'height' => '0'],
                    static::THUMBNAIL => (object)[
                        'width' => ImageProcessor::$thumbnailWidth,
                        'height' => ImageProcessor::$thumbnailHeight
                    ],
                    static::FEATURED => (object)['width' => 500, 'height' => 300],
                    static::PAGE => (object)['width' => 720, 'height' => 540],
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
            case static::PAGE:
                return 'pg';
            default:
                return [
                    static::ORIGINAL => '',
                    static::THUMBNAIL => 'tb',
                    static::FEATURED => 'ft',
                    static::PAGE => 'pg',
                ];
        }
    }

}
