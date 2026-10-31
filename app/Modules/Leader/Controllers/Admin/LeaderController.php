<?php

namespace App\Modules\Leader\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Leader;
use Illuminate\Http\Request;

class LeaderController extends Controller
{
    public function index(Request $request)
    {
        $query = Leader::query();
        if ($search = $request->search) {
            $query->where('name', 'like', "%$search%");
        }
        $items = $query->orderBy('order_no')->paginate(20)->withQueryString();
        return view('Leader::Admin.index', compact('items'));
    }

    public function create()
    {
        return view('Leader::Admin.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|max:255',
            'chucvu'        => 'nullable|max:255',
            'phone'         => 'nullable|max:30',
            'ngaysinh'      => 'nullable|date',
            'hoc_van'       => 'nullable|max:255',
            'years'         => 'nullable|max:50',
            'slogan'        => 'nullable|max:1000',
            'image'         => 'nullable|image|max:3072',
            'order_no'      => 'nullable|integer',
            // CKEditor fields
            'advantages'    => 'nullable|string',
            'progress'      => 'nullable|string',
            'achievements'  => 'nullable|string',
            'contributions' => 'nullable|string',
            'goals'         => 'nullable|string',
            'intro'         => 'nullable|string',
        ]);

        $data['status']       = $request->boolean('status', true);
        $data['ambassador']   = $request->boolean('ambassador');
        $data['is_ambassador']= $request->boolean('is_ambassador');
        $data['gender']       = $request->get('gender', 'male');

//        if ($request->hasFile('image')) {
//            $data['image'] = $request->file('image')->store('leaders', 'shared');
//        }
        if ($request->hasFile('image')) {
            $data['image'] = cdn_upload($request->file('image'), 'leaders');
        }
        Leader::create($data);
        return redirect()->route('admin.leaders.index')->with('success', 'Thêm lãnh đạo thành công!');
    }

    public function edit(Leader $leader)
    {
        return view('Leader::Admin.edit', compact('leader'));
    }

    public function update(Request $request, Leader $leader)
    {
        $data = $request->validate([
            'name'          => 'required|max:255',
            'chucvu'        => 'nullable|max:255',
            'phone'         => 'nullable|max:30',
            'ngaysinh'      => 'nullable|date',
            'hoc_van'       => 'nullable|max:255',
            'years'         => 'nullable|max:50',
            'slogan'        => 'nullable|max:1000',
            'image'         => 'nullable|image|max:3072',
            'order_no'      => 'nullable|integer',
            'advantages'    => 'nullable|string',
            'progress'      => 'nullable|string',
            'achievements'  => 'nullable|string',
            'contributions' => 'nullable|string',
            'goals'         => 'nullable|string',
            'intro'         => 'nullable|string',
        ]);

        $data['status']       = $request->boolean('status');
        $data['ambassador']   = $request->boolean('ambassador');
        $data['is_ambassador']= $request->boolean('is_ambassador');
        $data['gender']       = $request->get('gender', 'male');

        if ($request->hasFile('image')) {
            $data['image'] = cdn_upload($request->file('image'), 'leaders');
        }

        $leader->update($data);
        return redirect()->route('admin.leaders.index')->with('success', 'Cập nhật lãnh đạo thành công!');
    }

    public function destroy(Leader $leader)
    {
        $leader->delete();
        return back()->with('success', 'Xóa thành công!');
    }
}
