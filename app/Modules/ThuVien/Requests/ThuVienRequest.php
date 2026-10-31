<?php

namespace App\Modules\ThuVien\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ThuVienRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'             => 'required|max:255',
            'image'            => 'nullable|image|max:3072',
            'intro'            => 'nullable|string',
            'content'          => 'nullable|string',
            'meta_title'       => 'nullable|max:255',
            'meta_keywords'    => 'nullable|max:255',
            'meta_description' => 'nullable|max:500',
            'status'           => 'nullable',
            'featured'         => 'nullable',
        ];
    }
}
