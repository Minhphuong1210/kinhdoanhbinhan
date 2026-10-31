<?php

namespace App\Modules\Finance\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LichSuGiaoDich;
use App\Models\User;
use App\Models\YeuCauRutTien;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\YeuCauRutTienExport;

use Illuminate\Support\Facades\DB;
class YeuCauRutTienController extends Controller
{
    public function index(Request $request)
    {

//        dd($request->date_from,$request->date_to );

        $query = YeuCauRutTien::with(['user','nganHang.thanhPho'])->latest();
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }



        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $items = $query->paginate(100)->withQueryString();

//        dd($items);

        return view('Finance::Admin.yeu-cau-rut-tien.index', compact('items'));
    }

    public function update(Request $request, YeuCauRutTien $yeuCauRutTien)
    {
        $request->validate([
            'action' => 'required|in:approve,reject'
        ]);

        // chỉ xử lý khi đang chờ
        if ($yeuCauRutTien->status != 0) {
            return back()->withErrors([
                'error' => 'Yeu cau nay da duoc xu ly truoc do.'
            ]);
        }

        $user = User::find($yeuCauRutTien->user_id);

        if (! $user) {
            return back()->withErrors([
                'error' => 'Khong tim thay thanh vien.'
            ]);
        }

        /**
         * DUYỆT
         * chỉ đổi trạng thái
         */
        if ($request->action === 'approve') {

            $yeuCauRutTien->update([
                'status' => 1
            ]);

            return back()->with(
                'success',
                'Đã duyệt yêu cầu rút tiền của ' . ($user->name ?? '?')
            );
        }

        /**
         * TỪ CHỐI
         * cộng tiền lại + tạo lịch sử
         */
        DB::transaction(function () use ($user, $yeuCauRutTien) {

            // khóa dòng user
            $user = User::lockForUpdate()
                ->find($user->id);

            $viTruoc = $user->vi_tien;

            // cộng tiền lại
            $user->increment(
                'vi_tien',
                $yeuCauRutTien->so_tien
            );

            // lịch sử hoàn tiền
            LichSuGiaoDich::create([
                'admin_id'              => $user->id,
                'sender_id'             => $user->id,
                'receiver_id'           => $user->id,

                'surplus_before'        => $viTruoc,
                'amount'                => $yeuCauRutTien->so_tien,
                'surplus'               => $viTruoc + $yeuCauRutTien->so_tien,

                'surplus_vi_mua'        => $user->vi_tien_mua_hang,
                'surplus_vi_mua_befor'  => $user->vi_tien_mua_hang,

                'note' => 'Hoan lai tien do tu choi rut tien ve '
                    . $yeuCauRutTien->ngan_hang
                    . ' - '
                    . $yeuCauRutTien->stk,

                'type' => 5,
            ]);

            // đổi trạng thái từ chối
            $yeuCauRutTien->update([
                'status' => 2
            ]);
        });

        return back()->with(
            'success',
            'Đã từ chối và hoàn lại '
            . number_format($yeuCauRutTien->so_tien, 0, ',', '.')
            . 'đ cho '
            . ($user->name ?? '?')
        );
    }

    public function destroy(YeuCauRutTien $yeuCauRutTien)
    {
        $yeuCauRutTien->delete();
        return back()->with('success', 'Xoa thanh cong!');
    }

    public function exportExcel(Request $request)
    {
        $query = YeuCauRutTien::with(['user','nganHang.thanhPho'])->latest();
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $items = $query->get();
        $from = $request->date_from ? '_' . $request->date_from . '_' . ($request->date_to ?: now()->toDateString()) : '';
        $filename = 'Yeu-cau-rut-tien' . $from . '.xlsx';

        return Excel::download(new YeuCauRutTienExport($items), $filename);
    }

}
