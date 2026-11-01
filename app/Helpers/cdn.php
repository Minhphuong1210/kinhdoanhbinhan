<?php

use Illuminate\Support\Facades\Storage;

if (!function_exists('cdn')) {
    function cdn($path = '')
    {
        if (!$path) return '';

        return Storage::disk('shared')->url($path);
    }
}


if (!function_exists('asset_url')) {
    /** Ảnh lưu đường dẫn tương đối hoặc URL đầy đủ → URL hiển thị */
    function asset_url(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        return filter_var($path, FILTER_VALIDATE_URL) ? $path : cdn($path);
    }
}

if (!function_exists('normalize_search_text')) {
    /** Bỏ dấu tiếng Việt và chuyển chữ thường để so khớp */
    function normalize_search_text(string $text): string
    {
        $normalized = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);

        return strtolower($normalized !== false ? $normalized : $text);
    }
}

if (!function_exists('clean_html')) {
    /**
     * Lọc HTML từ CKEditor trước khi in ra trang: bỏ thẻ chạy được script,
     * thuộc tính on* và link javascript:.
     */
    function clean_html(?string $html): string
    {
        if ($html === null || trim($html) === '') {
            return '';
        }

        $dom = new DOMDocument();
        $previous = libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="utf-8"?><div>' . $html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $xpath = new DOMXPath($dom);

        foreach (iterator_to_array($xpath->query('//script|//style|//object|//embed|//form|//base|//meta|//link')) as $node) {
            $node->parentNode?->removeChild($node);
        }

        foreach ($xpath->query('//@*') as $attribute) {
            $name = strtolower($attribute->nodeName);
            $value = strtolower(preg_replace('/[\s\x00-\x1f]+/', '', $attribute->nodeValue));
            $isUrl = in_array($name, ['href', 'src', 'xlink:href', 'action', 'formaction'], true);

            if (str_starts_with($name, 'on') || ($isUrl && preg_match('/^(javascript|vbscript|data:text)/', $value))) {
                $attribute->ownerElement->removeAttribute($attribute->nodeName);
            }
        }

        $root = $dom->getElementsByTagName('div')->item(0);
        $output = '';

        foreach ($root->childNodes as $child) {
            $output .= $dom->saveHTML($child);
        }

        return $output;
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
