<?php

namespace App\Modules\Promotion\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GiftRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type'            => 'required|in:product,discount_percent,discount_amount',
            'gift_product_id' => 'nullable|required_if:type,product|exists:products,id',
            'quantity'        => 'nullable|required_if:type,product|integer|min:1',
            'discount_value'  => 'nullable|required_unless:type,product|numeric|min:0.01',
        ];
    }
}
