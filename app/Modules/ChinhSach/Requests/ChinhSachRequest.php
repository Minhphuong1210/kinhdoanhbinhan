<?php

namespace App\Modules\ChinhSach\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChinhSachRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'      => 'required|max:255',
            'content'    => 'nullable|string',
            'url'        => 'nullable|max:255',
            'image'      => 'nullable|image|max:2048',
            'vi_tri_web' => 'nullable|max:100',
            'status'     => 'nullable',
        ];
    }
}
