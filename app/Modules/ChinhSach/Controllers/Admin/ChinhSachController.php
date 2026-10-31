<?php

namespace App\Modules\ChinhSach\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChinhSach;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ChinhSachController extends Controller
{
    public function index(Request $request)
    {
        $query = ChinhSach::query();
        if ($search = $request->search) {
            $query->where('title', 'like', "%$search%");
        }
        $items = $query->latest()->paginate(20)->withQueryString();
        return view('ChinhSach::Admin.index', compact('items'));
    }

    public function create()
    {
        return view('ChinhSach::Admin.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'      => 'required|max:255',
            'content'    => 'nullable|string',
            'url'        => 'nullable|max:255',
            'image'      => 'nullable|image|max:2048',
            'vi_tri_web' => 'nullable|max:100',
        ]);
        $data['status'] = $request->boolean('status');

        if ($request->hasFile('image')) {
            $data['image'] = cdn_upload($request->file('image'), 'chinh_sach');
        }

        ChinhSach::create($data);
        return redirect()->route('admin.chinh-sachs.index')->with('success', 'Thêm thành công!');
    }

    public function edit(ChinhSach $chinhSach)
    {
        return view('ChinhSach::Admin.edit', compact('chinhSach'));
    }

    public function update(Request $request, ChinhSach $chinhSach)
    {
        $data = $request->validate([
            'title'      => 'required|max:255',
            'content'    => 'nullable|string',
            'url'        => 'nullable|max:255',
            'image'      => 'nullable|image|max:2048',
            'vi_tri_web' => 'nullable|max:100',
        ]);
        $data['status'] = $request->boolean('status');
        if ($request->hasFile('image')) {
            $data['image'] = cdn_upload($request->file('image'), 'chinh_sach');
        }
        $chinhSach->update($data);
        return redirect()->route('admin.chinh-sachs.index')->with('success', 'Cập nhật thành công!');
    }

    public function destroy(ChinhSach $chinhSach)
    {
        $chinhSach->delete();
        return back()->with('success', 'Xóa thành công!');
    }
}
