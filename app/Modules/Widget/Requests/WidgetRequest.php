<?php

namespace App\Modules\Widget\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WidgetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'location'   => 'required|max:100',
            'title'      => 'nullable|max:255',
            'name'       => 'nullable|max:255',
            'order_no'   => 'nullable|integer',
            'content'    => 'nullable|string',
            'link'       => 'nullable|max:500',
            'image'      => 'nullable|image|max:2048',
            'vi_tri_web' => 'nullable|max:100',
            'status'     => 'nullable',
        ];
    }
}
