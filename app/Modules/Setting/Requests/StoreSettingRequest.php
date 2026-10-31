<?php

namespace App\Modules\Setting\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'       => 'required|max:255',
            'value'      => 'nullable|string',
            'type'       => 'nullable|max:50',
            'vi_tri_web' => 'nullable|max:100',
            // Cùng giới hạn với khi cập nhật ở trang Cài đặt
            'file'       => $this->input('type') === 'video'
                ? 'nullable|file|mimes:mp4,webm,ogg|max:2048'
                : 'nullable|file|mimes:jpg,jpeg,png,webp,gif|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'file.mimes' => $this->input('type') === 'video'
                ? 'Video không đúng định dạng. Chỉ chấp nhận MP4, WebM hoặc OGG.'
                : 'File ảnh không đúng định dạng.',
            'file.max'   => 'File không được vượt quá 2MB.',
        ];
    }
}
