<?php

namespace App\Modules\GioiThieu\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GioiThieu;
use Illuminate\Http\Request;

class GioiThieuController extends Controller
{
    public function index(Request $request)
    {
        $items = GioiThieu::latest()->paginate(20)->withQueryString();
        return view('GioiThieu::Admin.index', compact('items'));
    }

    public function create()
    {
        return view('GioiThieu::Admin.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|max:255',
            'image'       => 'nullable|image|max:2048',
            'content'     => 'nullable|string',
            'vi_tri_text' => 'nullable|max:100',
            'vi_tri'      => 'nullable|max:100',
            'year'        => 'nullable|integer',
            'vi_tri_web'  => 'nullable|max:100',
        ]);
        // Ưu tiên vi_tri_text (dropdown có nghĩa) hơn vi_tri (text cũ)
        $data['vi_tri'] = $request->filled('vi_tri_text') ? $request->vi_tri_text : $request->vi_tri;
        unset($data['vi_tri_text']);
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('gioi-thieu', 'shared');
        }
        GioiThieu::create($data);
        return redirect()->route('admin.gioi-thieus.index')->with('success', 'Thêm thành công!');
    }

    public function edit(GioiThieu $gioiThieu)
    {
        return view('GioiThieu::Admin.edit', compact('gioiThieu'));
    }

    public function update(Request $request, GioiThieu $gioiThieu)
    {
        $data = $request->validate([
            'name'        => 'required|max:255',
            'image'       => 'nullable|image|max:2048',
            'content'     => 'nullable|string',
            'vi_tri_text' => 'nullable|max:100',
            'vi_tri'      => 'nullable|max:100',
            'year'        => 'nullable|integer',
            'vi_tri_web'  => 'nullable|max:100',
        ]);
        $data['vi_tri'] = $request->filled('vi_tri_text') ? $request->vi_tri_text : $request->vi_tri;
        unset($data['vi_tri_text']);
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('gioi-thieu', 'shared');
        }
        $gioiThieu->update($data);
        return redirect()->route('admin.gioi-thieus.index')->with('success', 'Cập nhật thành công!');
    }

    public function destroy(GioiThieu $gioiThieu)
    {
        $gioiThieu->delete();
        return back()->with('success', 'Xóa thành công!');
    }
}
