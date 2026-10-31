<?php
 
 namespace App\Modules\Shipping\Controllers\Admin;
 
 use App\Http\Controllers\Controller;
 use App\Models\ShippingCarrier;
 use Illuminate\Http\Request;
 
 class ShippingController extends Controller
 {
     public function settings()
     {
         $pancake = ShippingCarrier::where('alias', 'pancake')->first();
         return view('Shipping::Admin.settings', compact('pancake'));
     }
 
     public function saveSettings(Request $request)
     {
         $request->validate([
             'api_token' => 'required',
             'shop_id'   => 'required',
         ]);
 
         $carrier = ShippingCarrier::updateOrCreate(
             ['alias' => 'pancake'],
             [
                 'name'      => 'Pancake POS',
                 'api_token' => $request->api_token,
                 'config'    => ['shop_id' => $request->shop_id],
                 'is_active' => $request->has('is_active'),
             ]
         );
 
         return back()->with('success', 'Cập nhật cấu hình Pancake thành công!');
     }
 }
