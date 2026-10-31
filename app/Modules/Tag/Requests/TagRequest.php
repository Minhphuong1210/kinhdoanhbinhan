<?php

namespace App\Modules\Tag\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TagRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'   => 'required|max:255',
            'status' => 'nullable',
        ];
    }
}
