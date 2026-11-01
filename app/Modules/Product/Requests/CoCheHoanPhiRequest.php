<?php

namespace App\Modules\Product\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CoCheHoanPhiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $current = $this->route('co_che_hoan_phi');
        $parentRule = 'nullable|exists:co_che_hoan_phi,id';

        if ($current) {
            $parentRule .= '|not_in:' . $current->id;
        }

        return [
            'name' => 'required|string|max:225',
            'trang_hoat_dong' => 'required|in:combo,tu_chon',
            'tang_1' => 'nullable|numeric|min:0',
            'tang_2' => 'nullable|numeric|min:0',
            'tang_3' => 'nullable|numeric|min:0',
            'tang_4' => 'nullable|numeric|min:0',
            'tang_5' => 'nullable|numeric|min:0',
            'tang_6' => 'nullable|numeric|min:0',
            'parent_id' => $parentRule,
        ];
    }
}
