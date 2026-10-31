<?php

namespace App\Modules\Core\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Provinces;
use App\Models\Districts;
use App\Models\Wards;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\TongNganHang;

class ProfileController extends Controller
{
    /* ── Trang cập nhật địa chỉ (cũ, dùng cho middleware profile.complete) ── */

    public function edit()
    {
        $user = auth()->user();
        $provinces = Provinces::orderBy('name')->get(['id', 'name', 'type']);

        $districts = $user->province_id
            ? Districts::where('province_id', $user->province_id)->orderBy('name')->get(['id', 'name', 'prefix'])
            : collect();

        $wards = $user->district_id
            ? Wards::where('district_id', $user->district_id)->orderBy('name')->get(['id', 'name', 'prefix'])
            : collect();

        return view('Core::Admin.profile.edit', compact('user', 'provinces', 'districts', 'wards'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'province_id' => 'required',
            'district_id' => 'required',
            'ward_id' => 'required',
            'address' => 'required|string|max:500',
        ], [
            'province_id.required' => 'Vui lòng chọn Tỉnh/Thành phố.',
            'district_id.required' => 'Vui lòng chọn Quận/Huyện.',
            'ward_id.required' => 'Vui lòng chọn Xã/Phường.',
            'address.required' => 'Vui lòng nhập địa chỉ cụ thể.',
        ]);

        $user = auth()->user();
        $data = [
            'province_id' => $request->province_id,
            'district_id' => $request->district_id,
            'ward_id' => $request->ward_id,
            'address' => $request->address,
            'updated_at' => now(),
            'chi_nhanh_ngan_hang' => $request->chi_nhanh_ngan_hang ?? '',
        ];

        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }

        // Nếu có pin thì update
        if ($request->pin) {
            $data['pin'] = $request->pin; // nên hash luôn
        }


//        dd($data);

        $user->update($data);


        return redirect()->route('admin.dashboard')
            ->with('success', 'Cập nhật thông tin thành công!');
    }

    /* ── Trang Thông tin cá nhân (mới) ── */

    public function info()
    {
//        dd(123);

        $user = auth()->user();


        if (!$user) {
            return redirect()->route('admin.login');
        }


        $provinces = Provinces::orderBy('name')->get(['id', 'name', 'type']);

        $districts = $user->province_id
            ? Districts::where('province_id', $user->province_id)->orderBy('name')->get(['id', 'name', 'prefix'])
            : collect();

        $wards = $user->district_id
            ? Wards::where('district_id', $user->district_id)->orderBy('name')->get(['id', 'name', 'prefix'])
            : collect();

        $bankCities = \App\Models\ThanhPhoBanks::orderBy('name')->get(['id', 'name']);
        $ngan_hangs = TongNganHang::all(['id', 'name','ten_viet_tat']);
        $userBankCityId = $user->bank_area_id ?? null;
        $userTongNganHangId = $user->ngan_hang_id ?? null;
        return view('Core::Admin.profile.info', compact('user', 'provinces', 'districts', 'wards', 'bankCities', 'userBankCityId', 'userTongNganHangId', 'ngan_hangs'));
    }

    public function updateInfo(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('admin.login');
        }


//        dd($request->all());

        $request->validate([
            'name' => 'required|string',

            'tel' => [
                'required',
                'string',
                'max:20',
                'regex:/^(0|\+84)[0-9]{9}$/',
                Rule::unique('users', 'tel')->ignore($user->id)
            ],

            'province_id' => 'nullable',
            'district_id' => 'nullable',
            'ward_id' => 'nullable',

            'address' => 'nullable|string|max:500',

            'ngan_hang' => 'nullable|string|max:225',

            'stk' => [
                'nullable',
                'string',
                'max:225',
                'regex:/^[0-9]+$/'
            ],

            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',

        ], [
            // name
            'name.required' => 'Vui lòng nhập tên.',
            'name.max' => 'Tên không được vượt quá 255 ký tự.',

            // tel
            'tel.regex' => 'Số điện thoại không đúng định dạng.',
            'tel.unique' => 'Số điện thoại đã tồn tại.',

            // địa chỉ
            'province_id.exists' => 'Tỉnh/Thành phố không hợp lệ.',
            'district_id.exists' => 'Quận/Huyện không hợp lệ.',
            'ward_id.exists' => 'Phường/Xã không hợp lệ.',

            // address
            'address.max' => 'Địa chỉ không quá 500 ký tự.',

            // ngân hàng
            'ngan_hang.max' => 'Tên ngân hàng không quá 225 ký tự.',

            // stk
            'stk.regex' => 'Số tài khoản chỉ được chứa số.',

            // image
            'image.image' => 'File phải là hình ảnh.',
            'image.mimes' => 'Ảnh phải có định dạng jpg, jpeg, png hoặc webp.',
            'image.max' => 'Ảnh không được vượt quá 2MB.',
        ]);

        $data = $request->only([
            'name', 'tel', 'province_id', 'district_id',
            'ward_id', 'address', 'ngan_hang', 'stk','email', 'cccd', 'chu_tk','bank_area_id'
        ]);

        $data['ngan_hang_id'] = $request->tong_ngan_hang_id;
        $data['chi_nhanh_ngan_hang'] = $request->ngan_hang_chi_nhanh_id;

//        dd($data);

        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }

        // Nếu có pin thì update
        if ($request->pin) {
            $data['pin'] = $request->pin; // nên hash luôn
        }

//        dd($data);

        // Upload ảnh đại diện
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('avatars', 'shared');
        }

        $user->update($data);

        return redirect()->route('admin.profile.info')
            ->with('success', 'Cập nhật thông tin cá nhân thành công!');
    }
}
