<?php namespace App\Models\Media;

use App\Traits\Enumerable;
use Illuminate\Database\Eloquent\Model;

class MediaTypeImgFormat extends Model
{
    use Enumerable;


    const ORIGINAL = 1;
    const THUMBNAIL = 2;
    const FEATURED = 3;
    const PAGE = 4;
    const HD = 5;
    const JUMBO = 6;
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
                return (object)['width' => '200', 'height' => '200'];
            case static::FEATURED:
                return (object)['width' => '500', 'height' => '300'];
            case static::PAGE:
                return (object)['width' => '750', 'height' => '250'];
            case static::HD:
                return (object)['width' => '1280', 'height' => '720'];
            case static::JUMBO:
                return (object)['width' => '1920', 'height' => '500'];
            default:
                return [
                    static::ORIGINAL  => (object)['width' => '0', 'height' => '0'],
                    static::THUMBNAIL => (object)['width' => '200', 'height' => '200'],
                    static::FEATURED  => (object)['width' => '500', 'height' => '300'],
                    static::PAGE      => (object)['width' => '750', 'height' => '250'],
                    static::HD        => (object)['width' => '1280', 'height' => '720'],
                    static::JUMBO     => (object)['width' => '1920', 'height' => '500']
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
            case static::HD:
                return 'hd';
            case static::JUMBO:
                return 'jb';
            default:
                return [
                    static::ORIGINAL  => '',
                    static::THUMBNAIL => 'tb',
                    static::FEATURED  => 'ft',
                    static::PAGE      => 'pg',
                    static::HD        => 'hd',
                    static::JUMBO     => 'jb'
                ];
        }
    }

}
