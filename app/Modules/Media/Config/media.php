<?php

return [
    // local: lưu vào thư mục trên server. r2: lưu và lấy link từ Cloudflare R2
    'driver' => env('MEDIA_DRIVER', 'local'),

    'r2' => [
        'key'        => env('R2_ACCESS_KEY_ID'),
        'secret'     => env('R2_SECRET_ACCESS_KEY'),
        'bucket'     => env('R2_BUCKET'),
        'endpoint'   => env('R2_ENDPOINT'),
        'public_url' => env('R2_PUBLIC_URL'),
    ],

    'local' => [
        'root' => env('MEDIA_LOCAL_ROOT', storage_path('app/public/media')),
        'url'  => env('MEDIA_LOCAL_URL', '/storage/media'),
    ],

    'upload' => [
        // Thư mục API được phép ghi vào
        'folders'        => ['uploads', 'avatars'],
        'default_folder' => 'uploads',
        // Không có svg: file svg có thể chứa script
        'mimes'          => ['jpg', 'jpeg', 'png', 'webp', 'gif'],
        'max_kb'         => (int) env('MEDIA_MAX_KB', 5120),
    ],

    'routes' => [
        'prefix'     => 'api/media',
        'middleware' => ['api', 'auth:sanctum'],
        'throttle'   => 'throttle:30,1',
    ],
];
