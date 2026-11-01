<?php

namespace App\Modules\Product\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|max:255',
            'price' => 'nullable|numeric|min:0',
            'quantity' => 'nullable|integer|min:0',
            'purchase_price' => 'nullable|numeric|min:0',
            'old_price' => 'nullable|numeric|min:0',
            'price_hoan_tien' => 'nullable|numeric|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'parent_id' => 'nullable|exists:products,id',
            'co_che_hoan_phi_id' => 'nullable|exists:co_che_hoan_phi,id',
            'image' => 'nullable|image|max:3072',
            'image_extra' => 'nullable|array',
            'image_extra.*' => 'nullable|image|max:3072',
            'licenses' => 'nullable|array',
            'licenses.*' => 'nullable|image|max:3072',
            'keep_extra' => 'nullable|array',
            'keep_licenses' => 'nullable|array',
            'tags' => 'nullable|array',
            'tags.*' => 'integer|exists:tags,id',
            'description' => 'nullable|string',
            'content' => 'nullable|string',
            'instructions' => 'nullable|string',
            'origin' => 'nullable|string',
            'stt' => 'nullable|int',
            'video' => 'nullable|string|max:500',
            'active' => 'nullable',
            'hien_trang_chu' => 'nullable',
            'san_pham_noi_bat' => 'nullable',
            'san_pham_hien_nhu_baner' => 'nullable',
            'san_pham_hoan' => 'nullable',
        ];
    }
}
