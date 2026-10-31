<?php

namespace App\Modules\Category\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'      => 'required|max:255',
            'parent_id' => 'nullable|exists:categories,id',
            'image'     => 'nullable|image|max:2048',
            'active'    => 'nullable|boolean',
        ];
    }
}
