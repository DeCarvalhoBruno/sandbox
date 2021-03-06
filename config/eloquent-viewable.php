<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Eloquent Models
    |--------------------------------------------------------------------------
    */

    'models' => [

        /*
         * Here you can configure the default `View` model.
         */
        'view' => [

            'table_name' => 'views',
            'connection' => config('database.default', 'mysql'),

        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    */

    'cache' => [

        /*
         * Everything will be stored under the following key.
         */
        'key' => 'viewable_cache',

        /*
         * Here you may define the cache store that should be used.
         */
        'store' => config('cache.default', 'file'),

        /*
         * Default lifetime of cached views count in seconds.
         */
        'lifetime_in_seconds' => 3600,

    ],

    /*
    |--------------------------------------------------------------------------
    | Session Configuration
    |--------------------------------------------------------------------------
    */

    'session' => [

        /*
         * Everthing will be stored under the following key.
         */
        'key' => 'viewable_session',

    ],

    /*
    |--------------------------------------------------------------------------
    | Ignore Bots
    |--------------------------------------------------------------------------
    |
    | If you want to ignore bots, you can specify that here. The default
    | service that determines if a visitor is a crawler is a package
    | by JayBizzle called CrawlerDetect.
    |
    */
    'ignore_bots' => true,

    /*
    |--------------------------------------------------------------------------
    | Do Not Track Header
    |--------------------------------------------------------------------------
    |
    | If you want to honor the DNT header, you can specify that here. We won't
    | record views from visitors with the Do Not Track header.
    |
    */
    'honor_dnt' => false,

    /*
    |--------------------------------------------------------------------------
    | Cookies
    |--------------------------------------------------------------------------
    |
    | This package binds visitors to views using a cookie. If you want to
    | give this cookie a custom name, you can specify that here.
    |
    */

    'visitor_cookie_key' => 'viewable',

    /*
    |--------------------------------------------------------------------------
    | Ignore IP Addresses
    |--------------------------------------------------------------------------
    |
    | Ignore views of the following IP addresses.
    |
    */

    'ignored_ip_addresses' => [

        // '127.0.0.1',

    ],

];
