<?php

namespace App\Modules\Setting\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $all      = Setting::forWeb()->orderBy('name')->get()->groupBy('type');
        // Đưa tab 'general' lên đầu, rồi sắp xếp các tab còn lại theo alphabet
        $settings = collect();
        if ($all->has('general')) $settings->put('general', $all->get('general'));
        foreach ($all->sortKeys() as $key => $group) {
            if ($key !== 'general') $settings->put($key, $group);
        }
        return view('Setting::Admin.index', compact('settings'));
    }

    public function update(Request $request)
    {
        // Xử lý các setting thông thường
        foreach ($request->settings ?? [] as $id => $value) {
            Setting::where('id', $id)->update(['value' => $value]);
        }

        // Xử lý upload file (logo, favicon, ...)
        foreach ($request->file('uploads') ?? [] as $id => $file) {

            if ($file && $file->isValid()) {

                // lấy setting hiện tại
                $setting = Setting::find($id);

                // xoá file cũ nếu có
                if ($setting && $setting->value) {
                    cdn_delete($setting->value);
                }

                // upload file mới
                $path = cdn_upload($file, 'uploads/settings');

                // lưu DB (CHỈ lưu path)
                Setting::where('id', $id)->update([
                    'value' => $path
                ]);
            }
        }

        return back()->with('success', 'Lưu cài đặt thành công!');
    }

    public function create()
    {
        return view('Setting::Admin.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'       => 'required|max:255',
            'value'      => 'nullable|string',
            'type'       => 'nullable|max:50',
            'vi_tri_web' => 'nullable|max:100',
        ]);
        Setting::create($data);
        return redirect()->route('admin.settings.index')->with('success', 'Thêm cài đặt thành công!');
    }

    public function destroy(Setting $setting)
    {
        $setting->delete();
        return back()->with('success', 'Xóa thành công!');
    }
}
