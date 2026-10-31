<?php

namespace App\Modules\Leader\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LeaderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'          => 'required|max:255',
            'chucvu'        => 'nullable|max:255',
            'phone'         => 'nullable|max:30',
            'ngaysinh'      => 'nullable|date',
            'hoc_van'       => 'nullable|max:255',
            'years'         => 'nullable|max:50',
            'slogan'        => 'nullable|max:1000',
            'image'         => 'nullable|image|max:3072',
            'order_no'      => 'nullable|integer',
            // CKEditor fields
            'advantages'    => 'nullable|string',
            'progress'      => 'nullable|string',
            'achievements'  => 'nullable|string',
            'contributions' => 'nullable|string',
            'goals'         => 'nullable|string',
            'intro'         => 'nullable|string',
            'status'        => 'nullable',
            'ambassador'    => 'nullable',
            'is_ambassador' => 'nullable',
            'gender'        => 'nullable|string|max:20',
        ];
    }
}
