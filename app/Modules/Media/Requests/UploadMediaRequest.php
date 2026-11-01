<?php

namespace App\Modules\Media\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UploadMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'image'  => ['required', 'image', 'mimes:' . implode(',', config('media.upload.mimes')), 'max:' . config('media.upload.max_kb')],
            'folder' => ['nullable', 'string', Rule::in(config('media.upload.folders'))],
        ];
    }

    public function messages(): array
    {
        return [
            'image.required' => 'Vui long chon anh.',
            'image.image'    => 'File phai la hinh anh.',
            'image.mimes'    => 'Dinh dang anh khong duoc ho tro.',
            'image.max'      => 'Anh vuot qua dung luong cho phep.',
            'folder.in'      => 'Thu muc khong hop le.',
        ];
    }
}
