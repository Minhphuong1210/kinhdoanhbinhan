<?php

namespace App\Modules\Auth\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'      => 'required|string|max:255',
            'email'     => 'required|string|email:rfc,dns|max:255|unique:users,email',
            'tel'       => [
                'required',
                'string',
                'max:20',
                'regex:/^(0|\+84)[0-9]{9}$/', // chuẩn số VN
                'unique:users,tel',
            ],
            'password'  => 'required|string|min:6|max:255|confirmed',
            'address'   => 'nullable|string|max:500',
            'ngan_hang' => 'nullable|string|max:255',
            'stk'       => ['nullable', 'string', 'max:100', 'regex:/^[0-9]+$/'],
            'pin'       => ['nullable', 'digits_between:4,6', 'regex:/^[0-9]+$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Vui lòng nhập tên.',
            'name.max'      => 'Tên không được vượt quá 255 ký tự.',

            'email.required' => 'Vui lòng nhập email.',
            'email.email'    => 'Email không đúng định dạng.',
            'email.unique'   => 'Email này đã được sử dụng.',

            'tel.required' => 'Vui lòng nhập số điện thoại.',
            'tel.regex'    => 'Số điện thoại không đúng định dạng.',
            'tel.unique'   => 'Số điện thoại đã tồn tại.',

            'password.required'  => 'Vui lòng nhập mật khẩu.',
            'password.min'       => 'Mật khẩu tối thiểu 6 ký tự.',
            'password.confirmed' => 'Nhập lại mật khẩu không khớp.',

            'address.max' => 'Địa chỉ không quá 500 ký tự.',

            'ngan_hang.max' => 'Tên ngân hàng không quá 255 ký tự.',

            'stk.regex' => 'Số tài khoản chỉ được chứa số.',

            'pin.digits_between' => 'PIN phải từ 4 đến 6 chữ số.',
            'pin.regex'          => 'PIN chỉ được chứa số.',
        ];
    }
}
