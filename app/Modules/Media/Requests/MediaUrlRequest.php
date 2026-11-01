<?php

namespace App\Modules\Media\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MediaUrlRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Chỉ đường dẫn tương đối, không có ..
            'path' => ['required', 'string', 'max:500', 'regex:/^(?!.*\.\.)[A-Za-z0-9_\-\/.]+$/'],
        ];
    }
}
