<?php

namespace App\Modules\GioiThieu\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\GioiThieu\Models\GioiThieu;
use App\Modules\GioiThieu\Requests\GioiThieuRequest;
use App\Modules\GioiThieu\Services\GioiThieuService;
use Illuminate\Http\Request;

class GioiThieuController extends Controller
{
    public function __construct(private readonly GioiThieuService $gioiThieuService)
    {
    }

    public function index(Request $request)
    {
        $items = $this->gioiThieuService->list();

        return view('GioiThieu::Admin.index', compact('items'));
    }

    public function create()
    {
        return view('GioiThieu::Admin.create');
    }

    public function store(GioiThieuRequest $request)
    {
        $this->gioiThieuService->create($request->validated());

        return redirect()->route('admin.gioi-thieus.index')->with('success', 'Thêm thành công!');
    }

    public function edit(GioiThieu $gioiThieu)
    {
        return view('GioiThieu::Admin.edit', compact('gioiThieu'));
    }

    public function update(GioiThieuRequest $request, GioiThieu $gioiThieu)
    {
        $this->gioiThieuService->update($gioiThieu, $request->validated());

        return redirect()->route('admin.gioi-thieus.index')->with('success', 'Cập nhật thành công!');
    }

    public function destroy(GioiThieu $gioiThieu)
    {
        $this->gioiThieuService->delete($gioiThieu);

        return back()->with('success', 'Xóa thành công!');
    }
}
