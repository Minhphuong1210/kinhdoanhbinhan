<?php

namespace App\Modules\Setting\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Modules\Setting\Requests\StoreSettingRequest;
use App\Modules\Setting\Services\SettingService;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function __construct(private readonly SettingService $settingService)
    {
    }

    public function index()
    {
        $settings = $this->settingService->groupedForAdmin();

        return view('Setting::Admin.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $this->settingService->saveAll($request->input('settings', []), $request->file('uploads', []));

        return back()->with('success', 'Lưu cài đặt thành công!');
    }

    public function create()
    {
        return view('Setting::Admin.create');
    }

    public function store(StoreSettingRequest $request)
    {
        $this->settingService->createSetting($request->validated(), $request->file('file'));

        return redirect()->route('admin.settings.index')->with('success', 'Thêm cài đặt thành công!');
    }

    public function destroy(Setting $setting)
    {
        $this->settingService->delete($setting);

        return back()->with('success', 'Xóa thành công!');
    }
}
