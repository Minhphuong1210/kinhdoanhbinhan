<?php

namespace App\Modules\ThuVien\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ThuVien;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ThuVienController extends Controller
{
    public function index(Request $request)
    {
        $query = ThuVien::query();
        if ($search = $request->search) {
            $query->where('name', 'like', "%$search%");
        }
        $items = $query->latest()->paginate(20)->withQueryString();
        return view('ThuVien::Admin.index', compact('items'));
    }

    public function create()
    {
        return view('ThuVien::Admin.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'             => 'required|max:255',
            'image'            => 'nullable|image|max:3072',
            'intro'            => 'nullable|string',
            'content'          => 'nullable|string',
            'meta_title'       => 'nullable|max:255',
            'meta_keywords'    => 'nullable|max:255',
            'meta_description' => 'nullable|max:500',
        ]);
        $data['slug']     = Str::slug($data['name']);
        $data['status']   = $request->boolean('status');
        $data['featured'] = $request->boolean('featured');
        $data['admin_id'] = auth()->id();

        if ($request->hasFile('image')) {
            $data['image'] = cdn_upload($request->file('image'), 'thu_vien');
        }


        ThuVien::create($data);
        return redirect()->route('admin.thu-viens.index')->with('success', 'Thêm thành công!');
    }

    public function edit(ThuVien $thuVien)
    {
        return view('ThuVien::Admin.edit', compact('thuVien'));
    }

    public function update(Request $request, ThuVien $thuVien)
    {
        $data = $request->validate([
            'name'             => 'required|max:255',
            'image'            => 'nullable|image|max:3072',
            'intro'            => 'nullable|string',
            'content'          => 'nullable|string',
            'meta_title'       => 'nullable|max:255',
            'meta_keywords'    => 'nullable|max:255',
            'meta_description' => 'nullable|max:500',
        ]);
        $data['slug']     = Str::slug($data['name']);
        $data['status']   = $request->boolean('status');
        $data['featured'] = $request->boolean('featured');
        if ($request->hasFile('image')) {
            $data['image'] = cdn_upload($request->file('image'), 'thu_vien');
        }
        $thuVien->update($data);
        return redirect()->route('admin.thu-viens.index')->with('success', 'Cập nhật thành công!');
    }

    public function destroy(ThuVien $thuVien)
    {
        $thuVien->delete();
        return back()->with('success', 'Xóa thành công!');
    }
}
