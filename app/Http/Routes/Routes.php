<?php namespace App\Http\Routes;

class Routes
{

    protected static function i18nRouteNames($locale, $name)
    {
        if ($locale != null) {
            return sprintf('%s.%s', $locale, $name);
        }
        return $name;
    }

}