<?php

namespace App\Modules\Core\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LichSuGiaoDich;
use App\Models\User;
use App\Models\YeuCauRutTien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserFinanceController extends Controller
{
    /**
     * Lịch sử giao dịch của user hiện tại
     */
    public function transactions()
    {
        $user = auth()->user();
        $items = LichSuGiaoDich::where('admin_id', $user->id)
            ->with(['sender', 'receiver'])
            ->orderBy('id', 'desc')
            ->paginate(20);

        return view('Core::Admin.finance.transactions', compact('items', 'user'));
    }

    /**
     * Danh sách yêu cầu rút tiền của user hiện tại
     */
    public function withdrawals()
    {
        $user  = auth()->user();
        $items = YeuCauRutTien::with(['nganHang.thanhPho'])
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(20);
        $thanhPho = DB::table('thanh_pho_banks')->get();
        $nganHangs = DB::table('ngan_hangs')->get();

        $tenNganHangs = DB::table('tong_ngan_hang')->get();

        $userBankCityId = null;
        $userTongNganHangId = null;
        $userTongNganHangName = null;
        if ($user->ngan_hang_id) {
            $branch = \App\Models\NganHangBank::find($user->ngan_hang_id);
            if ($branch) {
                $userBankCityId = $branch->thanh_pho_id;
                $userTongNganHangId = $branch->tong_ngan_hang_id;
                if ($userTongNganHangId) {
                    $tnh = DB::table('tong_ngan_hang')->where('id', $userTongNganHangId)->first();
                    if ($tnh) {
                        $userTongNganHangName = $tnh->name;
                    }
                }
            }
        }

        return view('Core::Admin.finance.withdrawals', compact('items', 'user','thanhPho','nganHangs','tenNganHangs', 'userBankCityId', 'userTongNganHangName'));
    }

    /**
     * Gửi yêu cầu rút tiền mới
     */
    public function requestWithdrawal(Request $request)
    {
        $user = auth()->user();

        // khóa theo user trong 10 giây
        $lockKey = 'withdraw_lock_' . $user->id;

        $lock = Cache::lock($lockKey, 10);

        if (! $lock->get()) {
            return back()
                ->with('error', 'Yêu cầu rút tiền đang được xử lý, vui lòng đợi vài giây.')
                ->withInput();
        }




        try {

            $request->validate([
                'so_tien'   => 'required|numeric|min:10000',
                'ngan_hang' => 'required|max:100',
                'stk'       => 'required|max:50',
                'chu_tk'    => 'required|max:100',
                'cccd'      => 'required|max:100',
                'pin' =>'required'
            ]);

            if ($user->vi_tien < $request->so_tien) {
                return back()
                    ->with('error', 'Số dư ví không đủ.')
                    ->withInput();
            }


            if ($user->pin != $request->pin) {
                return back()
                    ->with('error', 'vui lòng nhập lại mã pin.')
                    ->withInput();
            }


            $check_acb = $request->ACB_Checkbox === 'yes';

            if (
                $request->ngan_hang !== 'Ngân hàng Á Châu'
                && !$request->filled('ngan_hang_id')
            ) {
                return back()
                    ->with('error', 'Vui lòng chọn chi nhánh ngân hàng.')
                    ->withInput();
            }





            DB::transaction(function () use ($user, $request,$check_acb) {

                $user = User::lockForUpdate()
                    ->find($user->id);



                // check lại trong transaction
                if ($user->vi_tien < $request->so_tien) {
                    throw new \Exception('Số dư không đủ');
                }

                $soTien = (float) $request->so_tien;

                $viTruoc = $user->vi_tien;

                // trừ tiền luôn
                $user->decrement('vi_tien', $soTien);

                // tạo yêu cầu
                $so_tien_sau_thue = $request->so_tien * 11.5 / 100;



                // Tạo yêu cầu rút tiền — tiền chưa trừ, chờ admin duyệt mới trừ
                YeuCauRutTien::create([
                    'user_id' => $user->id,
                    'so_tien' => $request->so_tien,
                    'so_tien_sau_thue' => $so_tien_sau_thue,
                    'cast_now' => $user->vi_tien,  // lưu số dư lúc tạo để admin tham khảo
                    'ngan_hang' => $request->ngan_hang,
                    'stk' => $request->stk,
                    'chu_tk' => $request->chu_tk,
                    'status' => 0, // 0 = chờ duyệt
                    'note' => $request->note,
                    'cccd'      => $request->cccd,
                    'ngan_hang_id'=> $request->ngan_hang_id,
                    'ACB_Checkbox'=>$check_acb,
                ]);

                // lịch sử
                LichSuGiaoDich::create([
                    'admin_id'       => $user->id,
                    'sender_id'      => $user->id,
                    'receiver_id'    => $user->id,
                    'surplus_before' => $viTruoc,
                    'amount'         => $soTien,
                    'surplus'        => $viTruoc - $soTien,
                    'note'           => 'Tao yeu cau rut tien',
                    'type'           => 3,
                ]);
            });

            return back()->with('success', 'Đã gửi yêu cầu rút tiền thành công.');

        } finally {
            // luôn nhả khóa
            optional($lock)->release();
        }
    }

    // --------------------------------------------------------
    //  Chuyển điểm nội bộ
    // --------------------------------------------------------

    public function lookupUser(Request $request)
    {
        $query = trim($request->q);
        if (strlen($query) < 3) {
            return response()->json(['found' => false]);
        }

        $sender = auth()->user();
        $user = User::where(function ($q) use ($query) {
            $q->where('tel', $query)
                ->orWhere('id', is_numeric($query) ? $query : -1)
                ->orWhere('email', $query);
        })
            ->where('id', '!=', $sender->id)
            ->select('id', 'name', 'tel', 'email')
            ->first();

        if (!$user) {
            return response()->json(['found' => false]);
        }

        return response()->json([
            'found' => true,
            'id' => $user->id,
            'name' => $user->name,
            'tel' => $user->tel,
        ]);
    }

    public function transferForm()
    {
        $user = auth()->user();
        return view('Core::Admin.finance.transfer', compact('user'));
    }

    public function processTransfer(Request $request)
    {
        $sender = auth()->user();


//        dd($sender);

        $request->validate([
            'nguoi_nhan' => 'required',
            'so_tien' => 'required|numeric|min:1000',
            'loai_vi' => 'required|in:vi_tien,vi_tien_mua_hang',
        ], [
            'nguoi_nhan.required' => 'Vui lòng nhập SĐT / ID / Email người nhận.',
            'so_tien.min' => 'Số tiền tối thiểu là 1.000đ.',
            'loai_vi.required' => 'Vui lòng chọn ví nhận.',
        ]);

        $soTien = (float) $request->so_tien;
        $loaiVi = $request->loai_vi;

        
        if ($sender->vi_tien < $soTien) {
            return back()->withErrors([
                'so_tien' => 'Số dư ví chính không đủ. Hiện có ' . number_format($sender->vi_tien, 0, ',', '.') . 'đ.'
            ])->withInput();
        }

        // Tìm người nhận
        $recipient = User::where(function ($q) use ($request) {
            $q->where('tel', $request->nguoi_nhan)
                ->orWhere('id', $request->nguoi_nhan)
                ->orWhere('email', $request->nguoi_nhan);
        })
            ->where('id', '!=', $sender->id)
            ->first();

        if (!$recipient) {
            return back()->withErrors([
                'nguoi_nhan' => 'Không tìm thấy người dùng.'
            ])->withInput();
        }

        DB::transaction(function () use ($sender, $recipient, $soTien, $loaiVi) {

            // ===== SENDER =====
            $senderBefore = $sender->vi_tien;

            $sender->decrement('vi_tien', $soTien);

            LichSuGiaoDich::create([
                'admin_id' => $sender->id,
                'receiver_id' => $recipient->id,
                'surplus_before' => $senderBefore,
                'amount' => $soTien,
                'surplus' => $senderBefore - $soTien,
                'note' => 'Chuyển ' . number_format($soTien) . 'đ tới ' . $recipient->name,
                'type' => 5,
            ]);

            // ===== RECIPIENT =====
            $recipientBefore = $recipient->$loaiVi;

          
            $recipient->increment($loaiVi, $soTien);

            $historyData = [
                'admin_id' => $recipient->id,
                'sender_id' => $sender->id,
                'amount' => $soTien,
                'note' => 'Nhận từ ' . $sender->name,
                'type' => 5,
            ];

            if ($loaiVi == 'vi_tien') {

                $historyData['surplus_before'] = $recipientBefore;
                $historyData['surplus'] = $recipientBefore + $soTien;

                // set ví kia = null cho sạch
                $historyData['surplus_vi_mua_befor'] = null;
                $historyData['surplus_vi_mua'] = null;

            } else {

                $historyData['surplus_vi_mua_befor'] = $recipientBefore;
                $historyData['surplus_vi_mua'] = $recipientBefore + $soTien;

                $historyData['surplus_before'] = null;
                $historyData['surplus'] = null;
            }

            LichSuGiaoDich::create($historyData);

        });

        return back()->with(
            'success',
            'Đã chuyển ' . number_format($soTien, 0, ',', '.') . 'đ tới ' . $recipient->name
        );
    }
}
