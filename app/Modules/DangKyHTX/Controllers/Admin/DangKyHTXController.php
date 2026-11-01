<?php

namespace App\Modules\DangKyHTX\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DangKyXacNhanHtx;
use App\Modules\DangKyHTX\Services\DangKyHtxService;
use Illuminate\Http\Request;

class DangKyHTXController extends Controller
{
    public function __construct(private readonly DangKyHtxService $dangKyService)
    {
    }

    public function index(Request $request)
    {
        $items = $this->dangKyService->list($request->only(['search', 'status']));

        return view('DangKyHTX::Admin.index', compact('items'));
    }

    public function show(DangKyXacNhanHtx $dangKyHTX)
    {
        return view('DangKyHTX::Admin.show', compact('dangKyHTX'));
    }

    public function update(Request $request, DangKyXacNhanHtx $dangKyHTX)
    {
        $data = $request->validate(['status' => 'required|in:pending,approved,rejected']);
        $dangKyHTX->update($data);

        return back()->with('success', 'Cập nhật trạng thái thành công!');
    }

    public function exportPdf(DangKyXacNhanHtx $dangKyHTX)
    {
        return $this->dangKyService->pdf($dangKyHTX)->download('chi-tiet-dang-ky-htx-' . $dangKyHTX->id . '.pdf');
    }
}
