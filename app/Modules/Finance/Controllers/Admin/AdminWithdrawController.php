<?php

namespace App\Modules\Finance\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LichSuGiaoDich;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminWithdrawController extends Controller
{
    /**
     * Hiển thị form rút tiền từ ví user (admin thao tác)
     */
    public function create()
    {
        $users = User::orderBy('name')
            ->select('id', 'name', 'tel', 'email', 'vi_tien')
            ->get();

        return view('Finance::Admin.admin-rut-tien.create', compact('users'));
    }

    /**
     * Tìm user qua AJAX
     */
    public function lookupUser(Request $request)
    {
        $query = trim($request->q);

        if (strlen($query) < 2) {
            return response()->json(['found' => false]);
        }

        $user = User::where(function ($q) use ($query) {
            $q->where('tel', 'like', "%{$query}%")
                ->orWhere('id', is_numeric($query) ? $query : -1)
                ->orWhere('email', 'like', "%{$query}%")
                ->orWhere('name', 'like', "%{$query}%");
        })
            ->select('id', 'name', 'tel', 'email', 'vi_tien')
            ->first();

        if (!$user) {
            return response()->json(['found' => false]);
        }

        return response()->json([
            'found'   => true,
            'id'      => $user->id,
            'name'    => $user->name,
            'tel'     => $user->tel,
            'email'   => $user->email,
            'vi_tien' => number_format($user->vi_tien ?? 0, 0, ',', '.'),
            'vi_tien_raw' => $user->vi_tien ?? 0,
        ]);
    }

    /**
     * Xử lý rút tiền từ ví user (admin thao tác)
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'so_tien' => 'required|numeric|min:1000',
            'ly_do'   => 'required|string|max:500',
        ], [
            'user_id.required' => 'Vui lòng chọn thành viên.',
            'user_id.exists'   => 'Thành viên không tồn tại.',
            'so_tien.required' => 'Vui lòng nhập số tiền.',
            'so_tien.min'      => 'Số tiền tối thiểu là 1.000đ.',
            'ly_do.required'   => 'Vui lòng nhập lý do rút tiền.',
        ]);

        $admin   = auth()->user();
        $soTien  = (float) $request->so_tien;
        $lyDo    = $request->ly_do;

        DB::transaction(function () use ($request, $admin, $soTien, $lyDo) {

            // Khóa dòng user để tránh race condition
            $user = User::lockForUpdate()->find($request->user_id);

            if (!$user) {
                throw new \Exception('Không tìm thấy thành viên.');
            }

            if ($user->vi_tien < $soTien) {
                throw new \Exception('Số dư ví của thành viên không đủ. Hiện có: ' . number_format($user->vi_tien, 0, ',', '.') . 'đ');
            }

            $viTruoc = $user->vi_tien;

            // Trừ tiền từ ví user
            $user->decrement('vi_tien', $soTien);

            // Tạo lịch sử giao dịch
            LichSuGiaoDich::create([
                'admin_id'       => $user->id,       // chủ sở hữu giao dịch = user bị trừ
                'sender_id'      => null,             // không có người gửi (admin thao tác)
                'receiver_id'    => null,
                'surplus_before' => $viTruoc,
                'amount'         => $soTien,
                'surplus'        => $viTruoc - $soTien,
                'note'           => 'Admin ' . $admin->name . ' rut tien: ' . $lyDo,
                'type'           => 6,                // type 6 = admin rút tiền
            ]);
        });

        return back()->with('success',
            'Đã rút thành công ' . number_format($soTien, 0, ',', '.') . 'đ từ ví của thành viên.'
        );
    }

    /**
     * Lịch sử rút tiền bởi admin
     */
    public function history(Request $request)
    {
        $query = LichSuGiaoDich::with(['user'])
            ->where('type', 6)
            ->orderByDesc('id');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('note', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('tel', 'like', "%{$search}%");
                    });
            });
        }

        $items = $query->paginate(50)->withQueryString();

        return view('Finance::Admin.admin-rut-tien.history', compact('items'));
    }
}
