<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Storage Disk
    |--------------------------------------------------------------------------
    |
    | The filesystem disk used for storing dynamic content uploads such as
    | section images and downloadable files.
    |
    */
    'disk' => env('DYNAMIC_CONTENT_DISK', 'public'),

    /*
    |--------------------------------------------------------------------------
    | Section Component Directory
    |--------------------------------------------------------------------------
    |
    | The Blade component directory used for frontend section components.
    | Section slugs from the sections config are resolved relative to this
    | directory, e.g. "dynamic" resolves "homepage-hero" to "dynamic.homepage-hero".
    |
    */
    'component_directory' => env('DYNAMIC_CONTENT_COMPONENT_DIR', 'dynamic'),

];
