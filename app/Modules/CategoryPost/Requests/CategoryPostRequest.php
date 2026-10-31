<?php

namespace App\Modules\CategoryPost\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'   => 'required|max:255',
            'active' => 'nullable|boolean',
        ];
    }
}
