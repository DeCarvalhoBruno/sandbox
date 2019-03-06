<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    const DB_LANGUAGE_ENGLISH_ID = 1;
    const DB_LANGUAGE_FRENCH_ID = 44;

    public $primaryKey = 'product_id';
    public $timestamps = false;

    /**
     * @return int
     */
    public static function getAppLanguageId(): int
    {
        switch (app()->getLocale()) {
            case "en":
                return self::DB_LANGUAGE_ENGLISH_ID;
                break;
            case "fr":
                return self::DB_LANGUAGE_FRENCH_ID;
                break;
            default:
                return self::DB_LANGUAGE_ENGLISH_ID;
                break;
        }
    }

    public static function getAppLanguageISO639()
    {
        switch (app()->getLocale()) {
            case "en":
                return 'en_US';
                break;
            case "fr":
                return 'fr_FR';
                break;
            default:
                return 'en_US';
                break;
        }

    }

}
