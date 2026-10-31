<?php

namespace App\Modules\Widget\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Widget;
use Illuminate\Http\Request;

class WidgetController extends Controller
{
    public function index(Request $request)
    {
        $items = Widget::orderBy('order_no')->paginate(20)->withQueryString();
        return view('Widget::Admin.index', compact('items'));
    }

    public function create()
    {
        return view('Widget::Admin.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'location' => 'required|max:100',
            'title'    => 'nullable|max:255',
            'name'     => 'nullable|max:255',
            'order_no' => 'nullable|integer',
            'content'  => 'nullable|string',
            'link'     => 'nullable|max:500',
            'image'    => 'nullable|image|max:2048',
            'vi_tri_web' => 'nullable|max:100',
        ]);
        $data['status'] = $request->boolean('status');

        if ($request->hasFile('image')) {
            $data['image'] = cdn_upload($request->file('image'), 'widgets');
        }

        Widget::create($data);
        return redirect()->route('admin.widgets.index')->with('success', 'Thêm thành công!');
    }

    public function edit(Widget $widget)
    {
        return view('Widget::Admin.edit', compact('widget'));
    }

    public function update(Request $request, Widget $widget)
    {
        $data = $request->validate([
            'location' => 'required|max:100',
            'title'    => 'nullable|max:255',
            'name'     => 'nullable|max:255',
            'order_no' => 'nullable|integer',
            'content'  => 'nullable|string',
            'link'     => 'nullable|max:500',
            'image'    => 'nullable|image|max:2048',
            'vi_tri_web' => 'nullable|max:100',
        ]);
        $data['status'] = $request->boolean('status');
        if ($request->hasFile('image')) {
            $data['image'] = cdn_upload($request->file('image'), 'widgets');
        }
        $widget->update($data);
        return redirect()->route('admin.widgets.index')->with('success', 'Cập nhật thành công!');
    }

    public function destroy(Widget $widget)
    {
        $widget->delete();
        return back()->with('success', 'Xóa thành công!');
    }
}
