<?php

namespace App\Modules\Media\Resources;

use App\Modules\Media\Services\MediaService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Dữ liệu ảnh trả về API. $resource là đường dẫn đã lưu.
 */
class MediaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'path' => $this->resource,
            'url'  => app(MediaService::class)->url($this->resource),
        ];
    }
}
