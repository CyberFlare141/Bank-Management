<?php

return [
    /*
    |--------------------------------------------------------------------------
    | View Storage Paths
    |--------------------------------------------------------------------------
    |
    | Most templates are stored in resources/views. Keep this default unless you
    | intentionally add extra view directories.
    |
    */
    'paths' => [
        resource_path('views'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Compiled View Path
    |--------------------------------------------------------------------------
    |
    | Laravel compiles Blade templates to PHP for performance. On this machine
    | the `storage/` tree may be non-writable, so we allow overriding the
    | compiled path via VIEW_COMPILED_PATH and resolve relative paths against
    | the project root.
    |
    */
    'compiled' => (function () {
        $compiled = env('VIEW_COMPILED_PATH');

        if (is_string($compiled) && $compiled !== '') {
            // If it's a relative path, resolve against the app base path.
            if (!preg_match('/^[A-Za-z]:\\\\/', $compiled) && !str_starts_with($compiled, DIRECTORY_SEPARATOR)) {
                return base_path($compiled);
            }

            return $compiled;
        }

        return realpath(storage_path('framework/views')) ?: storage_path('framework/views');
    })(),
];

