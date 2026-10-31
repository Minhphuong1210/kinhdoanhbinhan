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
        $data = $request->validate([
            'api_token' => 'required|string|max:255',
            'shop_id'   => 'required|string|max:100',
        ]);

        $this->carriers->savePancake($data['api_token'], $data['shop_id'], $request->has('is_active'));

        return back()->with('success', 'Cập nhật cấu hình Pancake thành công!');
    }
}
