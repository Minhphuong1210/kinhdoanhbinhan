<?php

namespace App\Modules\Post\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'             => 'required|max:255',
            'category_post_id' => 'nullable|exists:category_posts,id',
            'image'            => 'nullable|image|max:3072',
            'description'      => 'nullable|string',
            'content'          => 'nullable|string',
            'meta_title'       => 'nullable|max:255',
            'meta_keywords'    => 'nullable|max:255',
            'meta_description' => 'nullable|max:500',
            'vi_tri_web'       => 'required|in:htx,cong_ty',
            'status'           => 'nullable|string|max:50',
            'featured'         => 'nullable',
        ];
    }
}
