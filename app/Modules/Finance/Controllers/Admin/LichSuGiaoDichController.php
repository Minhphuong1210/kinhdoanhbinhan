<?php

namespace App\Modules\Finance\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LichSuGiaoDich;
use Illuminate\Http\Request;

class LichSuGiaoDichController extends Controller
{
    public function index(Request $request)
    {
        $query = LichSuGiaoDich::with(['sender', 'receiver'])->orderByDesc('id');
        if ($search = $request->search) {
            $query->where('note', 'like', "%$search%");
        }
        $items = $query->paginate(100)->withQueryString();
        return view('Finance::Admin.lich-su-giao-dich.index', compact('items'));
    }

    public function destroy(LichSuGiaoDich $lichSuGiaoDich)
    {
        $lichSuGiaoDich->delete();
        return back()->with('success', 'Xóa thành công!');
    }
}
