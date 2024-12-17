<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Image Driver
    |--------------------------------------------------------------------------
    |
    | Intervention Image supports "GD Library" and "Imagick" to process images
    | internally. You may choose one of them according to your PHP
    | configuration. By default PHP's "GD Library" implementation is used.
    |
    | Supported: "gd", "imagick"
    |
    */

    'driver' => 'gd',
    // Media size for validation (icon, logo, image, avatar, thumbnail, banner)
    'media' => [
        'icon' => [
            'width' => '300',
            'height' => '300',
            'size' => '1024',
        ],
        'logo' => [
            'width' => '300',
            'height' => '300',
            'size' => '1024',
        ],
        'image' => [
            'width' => '546',
            'height' => '640',
            'size' => '1024',
        ],
        'avatar' => [
            'width' => '300',
            'height' => '300',
            'size' => '1024',
        ],
        'thumbnail' => [
            'width' => '300',
            'height' => '300',
            'size' => '1024',
        ],
        'banner' => [
            'width' => '1200',
            'height' => '400',
            'size' => '5120',
        ],
        'secondaryBanner' => [
            'width' => '1200',
            'height' => '800',
            'size' => '5120',
        ],
        'product' => [
            'width' => '1200',
            'height' => '800',
            'size' => '2048',
        ],
        'news' => [
            'width' => '1200',
            'height' => '800',
            'size' => '2048',
        ],
        'services' => [
            'width' => '620',
            'height' => '400',
            'size' => '2048',
        ],

        'news_Paper_logo' => [
            'width' => '195',
            'height' => '44',
            'size' => '1024',
        ],

        'public_profile' => [
            'avatar' => [
                'width' => '300',
                'height' => '300',
                'size' => '1024',
            ],
            'banner' => [
                'width' => '1200',
                'height' => '400',
                'size' => '5120',
            ],
        ]
    ]

];
