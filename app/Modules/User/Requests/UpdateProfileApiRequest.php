<?php

namespace App\Modules\User\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileApiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->user()->id;

        return [
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'email:rfc,dns', 'max:255', 'unique:users,email,' . $userId],
            'tel' => ['nullable', 'string', 'max:20', 'regex:/^(0|\+84)[0-9]{9}$/', 'unique:users,tel,' . $userId],
            'address' => ['nullable', 'string', 'max:500'],
            'province_id' => ['nullable', 'exists:provinces,id'],
            'district_id' => ['nullable', 'exists:districts,id'],
            'ward_id' => ['nullable', 'exists:wards,id'],
            'cccd' => ['nullable', 'string', 'max:50'],
            'tong_ngan_hang_id' => ['nullable', 'exists:tong_ngan_hang,id'],
            'ngan_hang_id' => ['nullable', 'exists:tong_ngan_hang,id'],
            'ngan_hang_chi_nhanh_id' => ['nullable', 'exists:ngan_hangs,id'],
            'chi_nhanh_ngan_hang' => ['nullable'],
            'stk' => ['nullable', 'string', 'max:100', 'regex:/^[0-9]+$/'],
            'chu_tk' => ['nullable', 'string', 'max:255'],
            'bank_area_id' => ['nullable', 'exists:thanh_pho_banks,id'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }
}
