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
            $media_type = \App\Models\Media\Media::getConstantName($media_type);
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
     * @param int $entity
     * @param int $media_type
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

if (!function_exists('makeHexHashedUuid')) {
    /**
     *
     * @return string The 32 character UUID
     */
    function makeHexHashedUuid()
    {
        return \Ramsey\Uuid\Uuid::uuid5(\Ramsey\Uuid\Uuid::NAMESPACE_DNS,
            \Ramsey\Uuid\Uuid::uuid4()->toString())->getHex();
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

if (!function_exists('get_page_id')) {
    function get_page_id()
    {
        $router = app('router')->getCurrentRoute();
        if (!is_null($router)) {
            return substr(md5($router->getName()), 0, 10);
        }
        return 'undefined';
    }
}

if (!function_exists('is_hex_uuid_string')) {
    function is_hex_uuid_string($v)
    {
        return is_string($v) && strlen($v) == 32 && ctype_xdigit($v);
    }
}

if (!function_exists('get_locale_presentable_date_format')) {
    function get_locale_date_format()
    {
        switch (app()->getLocale()) {
            case 'fr':
                return 'm F Y @  h:m';
                break;
            default:
                return 'F jS, Y @ h:m';
                break;
        }
    }
}

if (!function_exists('response_json')) {
    function response_json($content = '', $status = 200, array $headers = [])
    {
        return app(\Illuminate\Contracts\Routing\ResponseFactory::class)
            ->json($content, $status, $headers);
    }

}