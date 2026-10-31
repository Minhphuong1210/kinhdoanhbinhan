<?php

namespace App\Modules\Promotion\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PromotionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => 'required|string|max:255',
            'month'       => 'required|integer|min:1|max:12',
            'year'        => 'required|integer|min:2020|max:2099',
            'description' => 'nullable|string',
            'is_active'   => 'nullable',
        ];
    }
}
