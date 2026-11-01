<?php

return [
    // Ảnh lưu và lấy link từ Cloudflare R2
    'r2' => [
        'key'        => env('R2_ACCESS_KEY_ID'),
        'secret'     => env('R2_SECRET_ACCESS_KEY'),
        'bucket'     => env('R2_BUCKET'),
        'endpoint'   => env('R2_ENDPOINT'),
        'public_url' => env('R2_PUBLIC_URL'),
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

    // Dùng cho lệnh images:clear-missing, dự án khác sửa theo DB của mình
    'cleanup' => [
        // Link cũ không còn dùng, ảnh trong nội dung HTML trỏ tới đây sẽ được xử lý
        'old_urls' => ['https://cdn.kinhdoanhbinhan.com/shared'],

        // bảng => cột chứa 1 đường dẫn ảnh
        'image_columns' => [
            'products'      => ['image'],
            'posts'         => ['image'],
            'users'         => ['image'],
            'categories'    => ['image'],
            'leaders'       => ['image'],
            'thu_vien'      => ['image'],
            'widgets'       => ['image'],
            'gioi_thieu'    => ['image'],
            'chinh_sach'    => ['image'],
            'notification'  => ['image'],
            'order_details' => ['image_product'],
        ],

        // bảng => cột JSON chứa danh sách đường dẫn ảnh
        'gallery_columns' => [
            'products' => ['image_extra', 'licenses'],
        ],

        // bảng => cột HTML có thể chứa ảnh
        'content_columns' => [
            'products'     => ['content', 'instructions', 'description'],
            'posts'        => ['content', 'description'],
            'notification' => ['content'],
            'chinh_sach'   => ['content'],
            'widgets'      => ['content'],
            'thu_vien'     => ['content', 'intro'],
            'gioi_thieu'   => ['content'],
            'leaders'      => ['advantages', 'progress', 'achievements', 'contributions', 'goals', 'intro'],
        ],
    ],
];
