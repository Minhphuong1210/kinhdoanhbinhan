<?php

namespace App\Modules\GioiThieu\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GioiThieuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'           => 'required|max:255',
            'image'          => 'nullable|image|max:2048',
            'content'        => 'nullable|string',
            'vi_tri_text'    => 'nullable|max:100',
            'vi_tri'         => 'nullable|max:100',
            'year'           => 'nullable|integer',
            'vi_tri_web'     => 'nullable|max:100',
            'nam_kinh_doanh' => 'nullable|max:50',
            'khach_hang'     => 'nullable|max:50',
            'san_pham'       => 'nullable|max:50',
        ];
    }
}
