<?php

namespace App\Modules\Shipping\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Shipping\Repositories\ShippingCarrierRepository;
use Illuminate\Http\Request;

class ShippingController extends Controller
{
    public function __construct(private readonly ShippingCarrierRepository $carriers)
    {
    }

    public function settings()
    {
        $pancake = $this->carriers->pancake();

        return view('Shipping::Admin.settings', compact('pancake'));
    }

    public function saveSettings(Request $request)
    {
        $current = $this->carriers->pancake();

        $data = $request->validate([
            'api_token' => [$current?->api_token ? 'nullable' : 'required', 'string', 'max:255'],
            'shop_id'   => 'required|string|max:100',
        ]);

        // Ô token luôn hiện rỗng: để trống nghĩa là giữ token cũ
        $token = $data['api_token'] ?? $current?->api_token;

        $this->carriers->savePancake($token, $data['shop_id'], $request->has('is_active'));

        return back()->with('success', 'Cập nhật cấu hình Pancake thành công!');
    }
}
