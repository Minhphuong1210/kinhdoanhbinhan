<?php

namespace App\Modules\Payment\Requests;

use App\Modules\Payment\Rules\ReceiverFullName;
use Illuminate\Foundation\Http\FormRequest;

class CreatePaymentLinkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id'      => 'required|exists:products,id',
            'qty'             => 'nullable|integer|min:1',
            'child_ids'       => 'nullable|array',
            'child_qtys'      => 'nullable|array',
            'mode'            => 'nullable|in:custom,custom_co_che',
            'receive_user'    => ['required', 'max:255', new ReceiverFullName()],
            'receive_phone'   => 'required|max:20',
            'province_id'     => 'required',
            'district_id'     => 'required',
            'ward_id'         => 'required',
            'receive_address' => 'required|max:500',
            'note'            => 'nullable|max:500',
            'confirmed'       => 'required|accepted',
        ];
    }

    public function messages(): array
    {
        return [
            'receive_user.required'    => 'Vui lòng nhập tên người nhận.',
            'receive_phone.required'   => 'Vui lòng nhập số điện thoại.',
            'province_id.required'     => 'Vui lòng chọn tỉnh/thành.',
            'district_id.required'     => 'Vui lòng chọn quận/huyện.',
            'ward_id.required'         => 'Vui lòng chọn phường/xã.',
            'receive_address.required' => 'Vui lòng nhập địa chỉ cụ thể.',
            'confirmed.required'       => 'Bạn cần tích xác nhận trước khi đặt mua.',
            'confirmed.accepted'       => 'Bạn cần tích xác nhận trước khi đặt mua.',
        ];
    }

    /**
     * Lỗi validate thì quay về trang mua (trang confirm là POST, không back() được).
     */
    protected function getRedirectUrl(): string
    {
        return self::buyUrl($this->all());
    }

    public static function buyUrl(array $input): string
    {
        $productId = $input['product_id'] ?? null;

        if (!$productId) {
            return route('admin.shop.index');
        }

        $params = ['product' => $productId];

        if (!empty($input['mode']) || !empty($input['child_ids'])) {
            $params['mode'] = in_array($input['mode'] ?? null, ['custom', 'custom_co_che'], true)
                ? $input['mode']
                : 'custom';
        }

        return route('admin.shop.buy', $params);
    }
}
