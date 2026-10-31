<?php

namespace App\Modules\Tag\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TagController extends Controller
{
    public function index(Request $request)
    {
        $query = Tag::withCount('products');
        if ($search = $request->search) {
            $query->where('name', 'like', "%$search%");
        }
        $tags = $query->latest()->paginate(20)->withQueryString();
        return view('Tag::Admin.index', compact('tags'));
    }

    public function create()
    {
        return view('Tag::Admin.create');
    }

    public function store(Request $request)
    {



        $data = $request->validate(['name' => 'required|max:255', 'status' => 'nullable']);
        $data['slug']   = Str::slug($data['name']);
$data['status'] = 0;
if($request->status == 'active'){
$data['status'] = 1;
}

        
        Tag::create($data);
        return redirect()->route('admin.tags.index')->with('success', 'Thêm tag thành công!');
    }

    public function edit(Tag $tag)
    {
        return view('Tag::Admin.edit', compact('tag'));
    }

    public function update(Request $request, Tag $tag)
    {
        $data = $request->validate(['name' => 'required|max:255', 'status' => 'nullable']);
        $data['slug']   = Str::slug($data['name']);
        $data['status'] = $request->get('status', 'active');
        $tag->update($data);
        return redirect()->route('admin.tags.index')->with('success', 'Cập nhật tag thành công!');
    }

    public function destroy(Tag $tag)
    {
        $tag->delete();
        return back()->with('success', 'Xóa tag thành công!');
    }
}
