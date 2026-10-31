<?php

namespace App\Modules\Cart\Requests;

use App\Modules\Cart\Services\CartService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AddCartItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'parent_product_id' => ['required', 'integer', 'exists:products,id'],
            'product_id'        => ['required', 'integer', 'exists:products,id'],
            'quantity'          => ['nullable', 'integer', 'min:1', 'max:' . CartService::MAX_QTY_PER_PRODUCT],
            'mode'              => ['nullable', Rule::in(CartService::MODES)],
        ];
    }

    public function messages(): array
    {
        return [
            'parent_product_id.required' => 'Vui long chon combo mua tu chon.',
            'parent_product_id.exists'   => 'Combo khong ton tai.',
            'product_id.required'        => 'Vui long chon san pham.',
            'product_id.exists'          => 'San pham khong ton tai.',
        ];
    }
}
