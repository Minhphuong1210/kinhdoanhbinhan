<?php

use Illuminate\Support\Facades\Storage;

if (!function_exists('cdn')) {
    function cdn($path = '')
    {
        if (!$path) return '';

        return Storage::disk('shared')->url($path);
    }
}


if (!function_exists('cdn_upload')) {
    function cdn_upload($file, $folder = '')
    {
        return Storage::disk('shared')->putFile($folder, $file);
    }
}

if (!function_exists('cdn_delete')) {
    function cdn_delete($path)
    {
        if (!$path) return false;

        // Nếu là URL full thì convert về path
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            $path = parse_url($path, PHP_URL_PATH);
            $path = preg_replace('#^/shared/#', '', $path);
        }

        // Xóa dấu / đầu nếu có
        $path = ltrim($path, '/');

        try {
            if (Storage::disk('shared')->exists($path)) {
                return Storage::disk('shared')->delete($path);
            }
        } catch (\Exception $e) {
            return false;
        }

        return false;
    }
}

if (!function_exists('cdn_base64')) {
    function cdn_base64($path)
    {
        if (!$path) return null;

        try {
            $disk = Storage::disk('shared');

            if (!$disk->exists($path)) {
                return null;
            }

            $file = $disk->get($path);
            $type = $disk->mimeType($path);

            return 'data:' . $type . ';base64,' . base64_encode($file);

        } catch (\Exception $e) {
            return null;
        }
    }
}
