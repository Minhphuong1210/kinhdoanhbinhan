<?php

namespace App\Modules\ChinhSach\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\ChinhSach\Models\ChinhSach;
use App\Modules\ChinhSach\Requests\ChinhSachRequest;
use App\Modules\ChinhSach\Services\ChinhSachService;
use Illuminate\Http\Request;

class ChinhSachController extends Controller
{
    public function __construct(private readonly ChinhSachService $chinhSachService)
    {
    }

    public function index(Request $request)
    {
        $items = $this->chinhSachService->list($request->search);

        return view('ChinhSach::Admin.index', compact('items'));
    }

    public function create()
    {
        return view('ChinhSach::Admin.create');
    }

    public function store(ChinhSachRequest $request)
    {
        $this->chinhSachService->create($request->validated());

        return redirect()->route('admin.chinh-sachs.index')->with('success', 'Thêm thành công!');
    }

    public function edit(ChinhSach $chinhSach)
    {
        return view('ChinhSach::Admin.edit', compact('chinhSach'));
    }

    public function update(ChinhSachRequest $request, ChinhSach $chinhSach)
    {
        $this->chinhSachService->update($chinhSach, $request->validated());

        return redirect()->route('admin.chinh-sachs.index')->with('success', 'Cập nhật thành công!');
    }

    public function destroy(ChinhSach $chinhSach)
    {
        $this->chinhSachService->delete($chinhSach);

        return back()->with('success', 'Xóa thành công!');
    }
}
