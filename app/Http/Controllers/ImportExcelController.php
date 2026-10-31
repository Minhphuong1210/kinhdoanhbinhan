<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\TongNganHang;
use App\Models\ThanhPhoBanks;
use App\Models\NganHangBank;
class ImportExcelController extends Controller
{

    public function form()
    {
        return view('import.index');
    }


    public function import(Request $request)
    {
        if (!$request->hasFile('file')) {
            return response()->json([
                'error' => 'Chưa chọn file'
            ], 400);
        }

        $file = $request->file('file');

        // đọc excel
        $data = Excel::toArray([], $file);

        DB::beginTransaction();

        try {

            $sheet1 = $data[0] ?? [];

            $added = 0;
            $updated = 0;
            $skipped = 0;

            foreach ($sheet1 as $index => $row) {

                // bỏ dòng tiêu đề
                if ($index == 0) {
                    continue;
                }

                /**
                 * Excel:
                 * 0 => Mã ngân hàng
                 * 1 => Tên ngân hàng
                 * 2 => Tỉnh
                 */

                $bankCode = trim($row[0] ?? '');
                $bankName = trim($row[1] ?? '');
                $cityName = trim($row[2] ?? '');

                // bỏ dòng rỗng
                if (
                    empty($bankCode) &&
                    empty($bankName) &&
                    empty($cityName)
                ) {
                    $skipped++;
                    continue;
                }

                // nếu thiếu tên ngân hàng thì bỏ qua
                if (empty($bankName)) {
                    $skipped++;
                    continue;
                }

                /**
                 * ==========================
                 * 1. TÌM / TẠO THÀNH PHỐ
                 * ==========================
                 */
                $city = ThanhPhoBanks::firstOrCreate([
                    'name' => $cityName
                ]);

                /**
                 * ==========================
                 * 2. CHECK NGÂN HÀNG
                 * ==========================
                 */

                // check theo id = mã ngân hàng
                $bank = NganHangBank::find($bankCode);

                if ($bank) {

                    // update nếu khác dữ liệu
                    $needUpdate = false;

                    if ($bank->name != $bankName) {
                        $bank->name = $bankName;
                        $needUpdate = true;
                    }

                    if ($bank->thanh_pho_id != $city->id) {
                        $bank->thanh_pho_id = $city->id;
                        $needUpdate = true;
                    }

                    if ($needUpdate) {
                        $bank->save();
                        $updated++;
                    }

                } else {

                    // thêm mới
                    NganHangBank::create([
                        'id'            => $bankCode,
                        'name'          => $bankName,
                        'thanh_pho_id'  => $city->id,
                    ]);

                    $added++;
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Import thành công',
                'added'   => $added,
                'updated' => $updated,
                'skipped' => $skipped,
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'error' => $e->getMessage(),
                'line'  => $e->getLine(),
            ], 500);
        }
    }
}