<?php

if (!function_exists('media_entity_path')) {
    /**
     * Retrieves the path of an image relative to the public folder
     *
     * @see \App\Models\SystemEntity
     * @see \App\Models\Media\MediaCategory
     *
     * @param string|int $entity
     * @param string|int $media_type
     * @param string $image The image file name
     *
     * @return string
     */
    function media_entity_path($entity, $media_type, $image)
    {
        if (is_numeric($entity)) {
            $entity = \App\Models\Entity::getConstantName($entity);
        }
        if (is_numeric($media_type)) {
            $category = \App\Models\Media\Media::getConstantName($media_type);
        }

        if (is_null($image)) {
            return getImagePlaceholderPath();
        }

        return sprintf('/media/%s/%s/%s', $entity, $media_type, $image);
    }

}

if (!function_exists('media_entity_root_path')) {
    /**
     * Retrieves the path of an image relative to the server root
     *
     * @param string $entity
     * @param string $media_type
     * @param string $image The image file name
     *
     * @return string
     */
    function media_entity_root_path($entity, $media_type, $image = null)
    {
        if (is_numeric($entity)) {
            $entity = \App\Models\Entity::getConstantName($entity);
        }
        if (is_numeric($media_type)) {
            $media_type = \App\Models\Media\Media::getConstantName($media_type);
        }

        return sprintf('%s/media/%s/%s/%s', public_path(), $entity, $media_type, $image);
    }

}

if (!function_exists('replaceLinksInTranslatedString')) {
    function replaceLinksInTranslatedString($link, $string)
    {
        $matches = [];
        preg_match('#\[([\s\d\p{L}[:punct:]]+)\]#u', $string, $matches);
        if (count($matches) == 2) {
            return str_replace($matches[0], sprintf('<a href="%s">%s</a>', $link, $matches[1]), $string);
        }

        return $string;
    }
}

if (!function_exists('encrypt')) {
    /**
     * Encrypt the given value.
     *
     * @param  string $value
     *
     * @return string
     */
    function encrypt($value)
    {
        return app('encrypter')->encrypt($value);
    }
}

if (!function_exists('getImagePlaceholderPath')) {
    function getImagePlaceholderPath()
    {
        return '/media/system/placeholders/no-thumb.png';
    }
}

if (!function_exists('getProfileImagePlaceholderPath')) {
    function getProfileImagePlaceholderPath()
    {
        return '/media/avatars/image/42-8fgiluf11zixx1soizcf2bufnjfyh.png';
    }
}

if (!function_exists('makeHexUuid')) {
    /**
     *
     * @return string The 32 character UUID
     */
    function makeHexUuid()
    {
        return \Ramsey\Uuid\Uuid::uuid4()->getHex();
    }
}

if (!function_exists('encodeHashIDs')) {
    function encodeHashIDs()
    {
        $arguments = func_get_args();
        $string = [];
        foreach ($arguments as $argument) {
            if ($argument > 0) {
                $string[] = encodeHashID($argument);
            } else {
                $string[] = strtolower(rand(10000, 9999999));
            }
        }

        return implode('-', $string);
    }
}

if (!function_exists('decodeHashIDs')) {
    /**
     * @param string $id
     *
     * @return array
     */
    function decodeHashIDs($id)
    {
        $idList = [];
        if (preg_match('#[\d\-]+#', $id)) {
            $ids = explode('-', $id);
            foreach ($ids as $id) {
                //A transmitted id of 0 is obfuscated as a random series of 5 to 7 numbers
                if (preg_match('#^[0-9]{5,7}$#', $id)) {
                    $idList[] = 0;
                    //Usable IDs contain 8 to 10 numbers
                } else {
                    if (preg_match('#^[0-9]{8,10}$#', $id)) {
                        $idList[] = decodeHashID($id);
                    }
                }
            }
        }

        return $idList;
    }
}

if (!function_exists('encodeHashID')) {
    function encodeHashID($id)
    {
        $hasher = new \Jenssegers\Optimus\Optimus(275604547, 880216171, 847305208);

        return $hasher->encode($id);
    }
}

if (!function_exists('decodeHashID')) {
    function decodeHashID($id)
    {
        if (is_numeric($id)) {
            $hasher = new \Jenssegers\Optimus\Optimus(275604547, 880216171, 847305208);

            return $hasher->decode($id);
        }

        return null;
    }
}

if (!function_exists('getTmpMediaSessionKey')) {
    function getTmpMediaSessionKey($entityId, $mediaCategoryID)
    {
        return sprintf('media_%s_%s', $entityId,
            $mediaCategoryID);
    }

}

if (!function_exists('slugify')) {
    function slugify($text, $strict = true)
    {
        $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
        // replace non letter or digits by -
        $text = preg_replace('~[^\\pL\d.]+~u', '-', $text);

        // trim
        $text = trim($text, '-');
        setlocale(LC_CTYPE, 'fr_FR.utf8');
        // transliterate
        if (function_exists('iconv')) {
            $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        }

        // lowercase
        $text = strtolower($text);
        // remove unwanted characters
        $text = preg_replace('~[^-\w.]+~', '', $text);
        if (empty($text)) {
            return 'empty_';
        }
        if ($strict) {
            $text = str_replace(".", "_", $text);
        }

        return $text;
    }
}

if (!function_exists('isFloat')) {
    function isFloat($value)
    {
        return ((int)$value != $value);
    }
}

if (!function_exists('route_i18n')) {
    /**
     * Generate the URL to a named route.
     *
     * @param  array|string $name
     * @param  mixed $parameters
     * @param  bool $absolute
     * @return string
     */
    function route_i18n($name, $parameters = [], $absolute = true)
    {
        $locale = app()->getLocale();
        if ($locale == config('app.fallback_locale')) {
            return app('url')->route($name, $parameters, $absolute);
        }
        return app('url')->route(sprintf('%s.%s', $locale, $name), $parameters, $absolute);
    }
}
