<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Documentation Enabled
    |--------------------------------------------------------------------------
    |
    | When disabled, the /docs routes are not registered and documentation
    | is unavailable. Set DOCS_ENABLED=false in production if you do not
    | want to expose the built-in documentation site.
    |
    */

    'enabled' => (bool) env('DOCS_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Documentation Path
    |--------------------------------------------------------------------------
    |
    | Absolute path to the static documentation root (HTML/CSS/assets).
    |
    */

    'path' => env('DOCS_PATH', base_path('docs')),

];
