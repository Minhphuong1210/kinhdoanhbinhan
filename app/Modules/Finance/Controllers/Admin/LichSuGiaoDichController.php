<?php

namespace App\Modules\Finance\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LichSuGiaoDich;
use App\Modules\Finance\Repositories\TransactionRepository;
use Illuminate\Http\Request;

class LichSuGiaoDichController extends Controller
{
    public function __construct(private readonly TransactionRepository $transactions)
    {
    }

    public function index(Request $request)
    {
        $items = $this->transactions->paginateForAdmin($request->search);

        return view('Finance::Admin.lich-su-giao-dich.index', compact('items'));
    }

    public function destroy(LichSuGiaoDich $lichSuGiaoDich)
    {
        $this->transactions->delete($lichSuGiaoDich);

        return back()->with('success', 'Xóa thành công!');
    }
}
