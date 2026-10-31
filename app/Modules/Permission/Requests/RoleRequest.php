<?php

namespace App\Modules\Permission\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'          => 'required|max:255|unique:roles,name,' . $this->route('role')?->id,
            'display_name'  => 'nullable|max:255',
            'permissions'   => 'nullable|array',
            'permissions.*' => 'integer|exists:permissions,id',
        ];
    }
}
