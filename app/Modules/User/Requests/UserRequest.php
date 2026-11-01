<?php

namespace App\Modules\User\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $user = $this->route('user');

        return [
            'name'        => 'required|max:255',
            'password'    => $user ? 'nullable|string|min:6' : 'required|min:6|confirmed',
            'password_confirmation' => $user ? 'nullable' : 'required',
            'tel'         => 'nullable|max:20|unique:users,tel,' . $user?->id,
            'parent_id'   => 'nullable',
            'invite_by'   => 'nullable',
            'pin'         => 'nullable|max:20',
            'ngan_hang'   => 'nullable|max:255',
            'stk'         => 'nullable|max:50',
            'vi_tien'     => 'nullable|numeric|min:0',
            'province_id' => 'nullable',
            'district_id' => 'nullable',
            'ward_id'     => 'nullable',
            'address'     => 'nullable|max:500',
            'image'       => 'nullable|image|max:2048',
            'active'      => 'nullable',
            'is_admin'    => 'nullable',
            'roles'       => 'nullable|array',
            'roles.*'     => 'integer|exists:roles,id',
        ];
    }
}
