<?php

namespace App\Modules\Tag\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Tag\Models\Tag;
use App\Modules\Tag\Requests\TagRequest;
use App\Modules\Tag\Services\TagService;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function __construct(private readonly TagService $tagService)
    {
    }

    public function index(Request $request)
    {
        $tags = $this->tagService->list($request->search);

        return view('Tag::Admin.index', compact('tags'));
    }

    public function create()
    {
        return view('Tag::Admin.create');
    }

    public function store(TagRequest $request)
    {
        $this->tagService->create($request->validated());

        return redirect()->route('admin.tags.index')->with('success', 'Thêm tag thành công!');
    }

    public function edit(Tag $tag)
    {
        return view('Tag::Admin.edit', compact('tag'));
    }

    public function update(TagRequest $request, Tag $tag)
    {
        $this->tagService->update($tag, $request->validated());

        return redirect()->route('admin.tags.index')->with('success', 'Cập nhật tag thành công!');
    }

    public function destroy(Tag $tag)
    {
        $this->tagService->delete($tag);

        return back()->with('success', 'Xóa tag thành công!');
    }
}
