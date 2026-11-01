<?php

use App\Modules\Media\Services\MediaService;

if (!function_exists('cdn')) {
    /** Link hiển thị của ảnh từ đường dẫn lưu trong DB */
    function cdn($path = '')
    {
        return app(MediaService::class)->url($path);
    }
}

if (!function_exists('cdn_upload')) {
    function cdn_upload($file, $folder = '')
    {
        return app(MediaService::class)->upload($file, $folder);
    }
}

if (!function_exists('cdn_delete')) {
    function cdn_delete($path)
    {
        return app(MediaService::class)->delete($path);
    }
}

if (!function_exists('cdn_base64')) {
    function cdn_base64($path)
    {
        return app(MediaService::class)->base64($path);
    }
}
