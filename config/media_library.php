<?php

declare(strict_types=1);

return [

    'disk' => env('MEDIA_LIBRARY_DISK', env('FILESYSTEM_DISK', 's3')),

    'directory' => env('MEDIA_LIBRARY_DIRECTORY', 'media-library'),

    'visibility' => env('MEDIA_LIBRARY_VISIBILITY', 'private'),

    /*
    |--------------------------------------------------------------------------
    | Allowed MIME Types
    |--------------------------------------------------------------------------
    |
    | Gunakan MIME whitelist sebagai lapisan validasi server-side.
    | Pola wildcard seperti image/* dan video/* didukung.
    |
    */
    'allowed_mime_types' => [
        'image/jpeg',
        'image/png',
        'image/webp',
        'application/pdf',
        'video/mp4',
    ],

    /*
    |--------------------------------------------------------------------------
    | Max Size Per MIME Pattern (KB)
    |--------------------------------------------------------------------------
    |
    | Nilai digunakan pada validasi backend setelah file tersimpan sementara.
    | Kunci dapat berupa exact MIME atau wildcard pattern.
    |
    */
    'max_size_kb' => [
        'image/*' => (int) env('MEDIA_LIBRARY_MAX_SIZE_IMAGE_KB', 5120),
        'application/pdf' => (int) env('MEDIA_LIBRARY_MAX_SIZE_DOCUMENT_KB', 10240),
        'video/*' => (int) env('MEDIA_LIBRARY_MAX_SIZE_VIDEO_KB', 51200),
        '*' => (int) env('MEDIA_LIBRARY_MAX_SIZE_DEFAULT_KB', 10240),
    ],
];
