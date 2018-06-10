<?php
/*
 * Set specific configuration variables here
 */
return [

    /*
    |--------------------------------------------------------------------------
    | Image Driver
    |--------------------------------------------------------------------------
    | Avatar use Intervention Image library to process image.
    | Meanwhile, Intervention Image supports "GD Library" and "Imagick" to process images
    | internally. You may choose one of them according to your PHP
    | configuration. By default PHP's "GD Library" implementation is used.
    |
    | Supported: "gd", "imagick"
    |
    */
    'driver'    => 'gd',

    // Initial generator class
    'generator' => \Laravolt\Avatar\Generator\DefaultGenerator::class,

    // Whether all characters supplied must be replaced with their closest ASCII counterparts
    'ascii'    => true,

    // Image shape: circle or square
    'shape' => 'square',

    // Image width, in pixel
    'width'    => 128,

    // Image height, in pixel
    'height'   => 128,

    // Number of characters used as initials. If name consists of single word, the first N character will be used
    'chars'    => 2,

    // font size
    'fontSize' => 80,

    // convert initial letter in uppercase
    'uppercase' => false,

    // Fonts used to render text.
    // If contains more than one fonts, randomly selected based on name supplied
    'fonts'    => [public_path('fonts/aladin-v6-latin-regular.ttf')],

    // List of foreground colors to be used, randomly selected based on name supplied
    'foregrounds'   => [
        '#FFFFFF',
    ],

    // List of background colors to be used, randomly selected based on name supplied
    'backgrounds'   => [
        '#992B23',
        '#ad164a',
        '#89229b',
        '#58319c',
        '#3F51B5',
        '#125285',
        '#026491',
        '#008191',
        '#009688',
        '#3d8c40',
        '#8BC34A',
        '#919c28',
        '#ab8105',
        '#975a00',
        '#9c3515'
    ],

    'border'    => [
        'size'  => 1,

        // border color, available value are:
        // 'foreground' (same as foreground color)
        // 'background' (same as background color)
        // or any valid hex ('#aabbcc')
        'color' => 'foreground',
    ],
];
