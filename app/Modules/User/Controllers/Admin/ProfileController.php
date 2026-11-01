<?php

namespace App\Modules\User\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Location\Services\LocationService;
use App\Modules\User\Requests\UpdateInfoRequest;
use App\Modules\User\Services\ProfileService;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function __construct(
        private readonly ProfileService $profileService,
        private readonly LocationService $locations
    ) {
    }

    /* ── Trang cập nhật địa chỉ (cũ, dùng cho middleware profile.complete) ── */

    public function edit()
    {
        $user = auth()->user();
        $provinces = $this->locations->provinces(['id', 'name', 'type']);
        $districts = $this->locations->districtsOf($user->province_id, ['id', 'name', 'prefix']);
        $wards = $this->locations->wardsOf($user->district_id, ['id', 'name', 'prefix']);

        return view('Core::Admin.profile.edit', compact('user', 'provinces', 'districts', 'wards'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'province_id' => 'required',
            'district_id' => 'required',
            'ward_id' => 'required',
            'address' => 'required|string|max:500',
            'chi_nhanh_ngan_hang' => 'nullable',
            'password' => 'nullable|string|min:6|max:255',
            'pin' => 'nullable|digits_between:4,6',
        ], [
            'province_id.required' => 'Vui lòng chọn Tỉnh/Thành phố.',
            'district_id.required' => 'Vui lòng chọn Quận/Huyện.',
            'ward_id.required' => 'Vui lòng chọn Xã/Phường.',
            'address.required' => 'Vui lòng nhập địa chỉ cụ thể.',
        ]);

        $this->profileService->updateAddress(auth()->user(), $data);

        return redirect()->route('admin.dashboard')->with('success', 'Cập nhật thông tin thành công!');
    }

    /* ── Trang Thông tin cá nhân (mới) ── */

    public function info()
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('admin.login');
        }

        $provinces = $this->locations->provinces(['id', 'name', 'type']);
        $districts = $this->locations->districtsOf($user->province_id, ['id', 'name', 'prefix']);
        $wards = $this->locations->wardsOf($user->district_id, ['id', 'name', 'prefix']);
        $bankCities = $this->locations->bankAreas();
        $ngan_hangs = $this->locations->banks(['id', 'name', 'ten_viet_tat']);
        $userBankCityId = $user->bank_area_id ?? null;
        $userTongNganHangId = $user->ngan_hang_id ?? null;

        return view('Core::Admin.profile.info', compact('user', 'provinces', 'districts', 'wards', 'bankCities', 'userBankCityId', 'userTongNganHangId', 'ngan_hangs'));
    }

    public function updateInfo(UpdateInfoRequest $request)
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('admin.login');
        }

        $this->profileService->updateInfo($user, $request->validated());

        return redirect()->route('admin.profile.info')->with('success', 'Cập nhật thông tin cá nhân thành công!');
    }
}
