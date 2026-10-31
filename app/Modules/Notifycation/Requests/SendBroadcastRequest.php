<?php

namespace App\Modules\Notifycation\Requests;

use App\Modules\Notifycation\Services\BroadcastNotificationService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SendBroadcastRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'   => ['required', 'string', 'max:255'],
            // HTML từ CKEditor (cột content kiểu text)
            'content' => ['required', 'string', 'max:60000'],
            'vi_tri'  => ['required', Rule::in(array_keys(BroadcastNotificationService::TARGETS))],
            'image'   => ['nullable', 'image', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'   => 'Vui lòng nhập tiêu đề.',
            'content.required' => 'Vui lòng nhập nội dung.',
            'vi_tri.required'  => 'Vui lòng chọn app nhận thông báo.',
            'vi_tri.in'        => 'App nhận thông báo không hợp lệ.',
        ];
    }
}
