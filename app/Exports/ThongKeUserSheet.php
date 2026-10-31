<?php

namespace App\Exports;

use App\Models\LichSuGiaoDich;
use App\Models\YeuCauRutTien;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class ThongKeUserSheet implements FromCollection, WithHeadings, WithTitle
{
    protected $items;

    public function __construct($items)
    {
        $this->items = $items;
    }

    public function title(): string
    {
        return 'Thống kê user';
    }

    public function headings(): array
    {
        return [
            'Tên',
            'SĐT',
            'CCCD',
            'Số lần mua hàng',
            'Tổng tiền mua hàng',
            'Tổng hoa hồng',
            'Từ ngày',
            'Đến ngày',
        ];
    }

    public function collection()
    {
        $rows = collect();

        $grouped = $this->items->groupBy('user_id');

        foreach ($grouped as $userId => $withdrawsGroup) {

            $user = $withdrawsGroup->first()->user;

            $withdraws = YeuCauRutTien::where('user_id', $userId)
                ->where('status', 1)
                ->orderBy('created_at')
                ->get();

            if ($withdraws->isEmpty()) {
                continue;
            }

            $previousDate = null;

            foreach ($withdraws as $withdraw) {

                $toDate = $withdraw->created_at;

                // fromDate = lần trước đó, nếu không có thì lấy từ rất sớm
                $fromDate = $previousDate ?? '1970-01-01 00:00:00';

                $query = LichSuGiaoDich::where('admin_id', $userId)
                    ->whereBetween('created_at', [$fromDate, $toDate]);

                $buyCount = (clone $query)
                    ->where('type', 1)
                    ->count();

                $totalBuy = (clone $query)
                    ->where('type', 1)
                    ->sum('amount');

                $totalCommission = (clone $query)
                    ->where('type', 2)
                    ->sum('amount');

                $rows->push([
                    $user?->name ?? '',
                    $user?->tel ?? '',
                    $withdraw?->cccd ?? '',
                    $buyCount,
                    $totalBuy,
                    $totalCommission,
                    $fromDate != '1970-01-01 00:00:00' ? \Carbon\Carbon::parse($fromDate)->format('d/m/Y H:i') : '',
                    $toDate->format('d/m/Y H:i'),
                ]);

                // cập nhật mốc
                $previousDate = $toDate;
            }
        }

        return $rows;
    }
}
