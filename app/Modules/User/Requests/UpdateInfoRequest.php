<?php

namespace App\Modules\User\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateInfoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->user()?->id;

        return [
            'name' => 'required|string|max:255',
            'tel' => [
                'required',
                'string',
                'max:20',
                'regex:/^(0|\+84)[0-9]{9}$/',
                Rule::unique('users', 'tel')->ignore($userId),
            ],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'province_id' => 'nullable',
            'district_id' => 'nullable',
            'ward_id' => 'nullable',
            'address' => 'nullable|string|max:500',
            'ngan_hang' => 'nullable|string|max:225',
            'stk' => ['nullable', 'string', 'max:225', 'regex:/^[0-9]+$/'],
            'cccd' => 'nullable|string|max:50',
            'chu_tk' => 'nullable|string|max:255',
            'bank_area_id' => 'nullable',
            'tong_ngan_hang_id' => 'nullable',
            'ngan_hang_chi_nhanh_id' => 'nullable',
            'password' => 'nullable|string|min:6|max:255',
            'pin' => ['nullable', 'digits_between:4,6'],
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Vui lòng nhập tên.',
            'name.max' => 'Tên không được vượt quá 255 ký tự.',
            'tel.regex' => 'Số điện thoại không đúng định dạng.',
            'tel.unique' => 'Số điện thoại đã tồn tại.',
            'email.email' => 'Email không đúng định dạng.',
            'email.unique' => 'Email này đã được sử dụng.',
            'address.max' => 'Địa chỉ không quá 500 ký tự.',
            'ngan_hang.max' => 'Tên ngân hàng không quá 225 ký tự.',
            'stk.regex' => 'Số tài khoản chỉ được chứa số.',
            'image.image' => 'File phải là hình ảnh.',
            'image.mimes' => 'Ảnh phải có định dạng jpg, jpeg, png hoặc webp.',
            'image.max' => 'Ảnh không được vượt quá 2MB.',
        ];
    }
}
