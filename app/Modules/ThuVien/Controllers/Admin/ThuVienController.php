<?php

namespace App\Modules\ThuVien\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\ThuVien\Models\ThuVien;
use App\Modules\ThuVien\Requests\ThuVienRequest;
use App\Modules\ThuVien\Services\ThuVienService;
use Illuminate\Http\Request;

class ThuVienController extends Controller
{
    public function __construct(private readonly ThuVienService $thuVienService)
    {
    }

    public function index(Request $request)
    {
        $items = $this->thuVienService->list($request->search);

        return view('ThuVien::Admin.index', compact('items'));
    }

    public function create()
    {
        return view('ThuVien::Admin.create');
    }

    public function store(ThuVienRequest $request)
    {
        $this->thuVienService->create($request->validated() + ['admin_id' => auth()->id()]);

        return redirect()->route('admin.thu-viens.index')->with('success', 'Thêm thành công!');
    }

    public function edit(ThuVien $thuVien)
    {
        return view('ThuVien::Admin.edit', compact('thuVien'));
    }

    public function update(ThuVienRequest $request, ThuVien $thuVien)
    {
        $this->thuVienService->update($thuVien, $request->validated());

        return redirect()->route('admin.thu-viens.index')->with('success', 'Cập nhật thành công!');
    }

    public function destroy(ThuVien $thuVien)
    {
        $this->thuVienService->delete($thuVien);

        return back()->with('success', 'Xóa thành công!');
    }
}
