<?php

namespace App\Modules\Permission\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'         => 'required|max:255|unique:permissions,name,' . $this->route('permission')?->id,
            'display_name' => 'nullable|max:255',
        ];
    }
}
