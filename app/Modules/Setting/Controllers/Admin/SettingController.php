<?php

namespace App\Modules\Setting\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Modules\Core\ViewComposers\FrontendLayoutComposer;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $all = Setting::forWeb()
            ->orderBy('name')
            ->get()
            ->groupBy('type');

        // Đưa tab general lên đầu
        $settings = collect();

        if ($all->has('general')) {
            $settings->put('general', $all->get('general'));
        }

        // Các tab còn lại sort alphabet
        foreach ($all->sortKeys() as $key => $group) {
            if ($key !== 'general') {
                $settings->put($key, $group);
            }
        }

        return view(
            'Setting::Admin.index',
            compact('settings')
        );
    }


    public function update(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | 1. Cập nhật setting dạng text / textarea
        |--------------------------------------------------------------------------
        */

        foreach ($request->input('settings', []) as $id => $value) {

            $setting = Setting::find($id);

            if (!$setting) {
                continue;
            }

            // Không update file thông qua settings[]
            if (in_array($setting->type, ['image', 'video']) || $setting->isVideo()) {
                continue;
            }

            Setting::where('id', $id)->update([
                'value' => $value
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | 2. Xử lý upload file
        |--------------------------------------------------------------------------
        */

        foreach ($request->file('uploads', []) as $id => $file) {

            if (!$file || !$file->isValid()) {
                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | Lấy setting
            |--------------------------------------------------------------------------
            */

            $setting = Setting::find($id);

            if (!$setting) {
                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | Xác định có phải video không
            |--------------------------------------------------------------------------
            |
            | Danh sách tên setting video: Setting::VIDEO_NAMES
            |
            */

            $isVideo = $setting->isVideo();


            /*
            |--------------------------------------------------------------------------
            | VIDEO
            |--------------------------------------------------------------------------
            */

            if ($isVideo) {

                $extension = strtolower(
                    $file->getClientOriginalExtension()
                );

                $allowedExtensions = [
                    'mp4',
                    'webm',
                    'ogg'
                ];

                /*
                |--------------------------------------------------------------------------
                | Kiểm tra định dạng
                |--------------------------------------------------------------------------
                */

                if (!in_array($extension, $allowedExtensions)) {

                    return back()
                        ->withInput()
                        ->withErrors([
                            'uploads.' . $id =>
                                'Video không đúng định dạng. Chỉ chấp nhận MP4, WebM hoặc OGG.'
                        ]);
                }


                /*
                |--------------------------------------------------------------------------
                | Kiểm tra dung lượng
                |
                | 2MB = 2 * 1024 * 1024 bytes
                |--------------------------------------------------------------------------
                */

                if ($file->getSize() > 2 * 1024 * 1024) {

                    return back()
                        ->withInput()
                        ->withErrors([
                            'uploads.' . $id =>
                                'Video không được vượt quá 2MB.'
                        ]);
                }

            }


            /*
            |--------------------------------------------------------------------------
            | IMAGE
            |--------------------------------------------------------------------------
            */

            else {

                $extension = strtolower(
                    $file->getClientOriginalExtension()
                );

                $allowedExtensions = [
                    'jpg',
                    'jpeg',
                    'png',
                    'webp',
                    'gif',
                    'svg'
                ];

                /*
                |--------------------------------------------------------------------------
                | Kiểm tra định dạng ảnh
                |--------------------------------------------------------------------------
                */

                if (!in_array($extension, $allowedExtensions)) {

                    return back()
                        ->withInput()
                        ->withErrors([
                            'uploads.' . $id =>
                                'File ảnh không đúng định dạng.'
                        ]);
                }


                /*
                |--------------------------------------------------------------------------
                | Kiểm tra dung lượng ảnh
                |--------------------------------------------------------------------------
                */

                if ($file->getSize() > 2 * 1024 * 1024) {

                    return back()
                        ->withInput()
                        ->withErrors([
                            'uploads.' . $id =>
                                'Ảnh không được vượt quá 2MB.'
                        ]);
                }
            }


            /*
            |--------------------------------------------------------------------------
            | Xóa file cũ
            |--------------------------------------------------------------------------
            */

            if ($setting->value) {

                try {

                    cdn_delete(
                        $setting->value
                    );

                } catch (\Exception $e) {

                    \Log::warning(
                        'Không thể xóa file setting cũ',
                        [
                            'setting_id' => $setting->id,
                            'path'       => $setting->value,
                            'error'      => $e->getMessage()
                        ]
                    );
                }
            }


            /*
            |--------------------------------------------------------------------------
            | Upload file mới
            |--------------------------------------------------------------------------
            */

            $path = cdn_upload(
                $file,
                'uploads/settings'
            );


            /*
            |--------------------------------------------------------------------------
            | Lưu path vào database
            |--------------------------------------------------------------------------
            */

            $setting->update([
                'value' => $path
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Thành công
        |--------------------------------------------------------------------------
        */

        // Website (header, chân trang) dùng cache thông tin doanh nghiệp
        FrontendLayoutComposer::forget();

        return back()->with(
            'success',
            'Lưu cài đặt thành công!'
        );
    }


    public function create()
    {
        return view(
            'Setting::Admin.create'
        );
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            'name'       => 'required|max:255',
            'value'      => 'nullable|string',
            'type'       => 'nullable|max:50',
            'vi_tri_web' => 'nullable|max:100',
            // Cùng giới hạn với khi cập nhật ở trang Cài đặt
            'file'       => $request->input('type') === 'video'
                ? 'nullable|file|mimes:mp4,webm,ogg|max:2048'
                : 'nullable|file|mimes:jpg,jpeg,png,webp,gif,svg|max:2048',
        ], [
            'file.mimes' => $request->input('type') === 'video'
                ? 'Video không đúng định dạng. Chỉ chấp nhận MP4, WebM hoặc OGG.'
                : 'File ảnh không đúng định dạng.',
            'file.max'   => 'File không được vượt quá 2MB.',
        ]);

        // Loại image / video: lưu file lên CDN, value là đường dẫn file
        if (in_array($data['type'] ?? null, ['image', 'video'], true)) {
            $data['value'] = $request->hasFile('file')
                ? cdn_upload($request->file('file'), 'uploads/settings')
                : null;
        }

        unset($data['file']);

        Setting::create($data);
        FrontendLayoutComposer::forget();

        return redirect()
            ->route('admin.settings.index')
            ->with(
                'success',
                'Thêm cài đặt thành công!'
            );
    }


    public function destroy(Setting $setting)
    {
        /*
        |--------------------------------------------------------------------------
        | Xóa file nếu setting có file
        |--------------------------------------------------------------------------
        */

        if (
            $setting->value &&
            (
                $setting->isVideo() ||
                in_array($setting->type, ['image', 'video'])
            )
        ) {

            try {

                cdn_delete(
                    $setting->value
                );

            } catch (\Exception $e) {

                \Log::warning(
                    'Không thể xóa file setting',
                    [
                        'setting_id' => $setting->id,
                        'path'       => $setting->value,
                        'error'      => $e->getMessage()
                    ]
                );
            }
        }


        $setting->delete();
        FrontendLayoutComposer::forget();

        return back()->with(
            'success',
            'Xóa thành công!'
        );
    }
}