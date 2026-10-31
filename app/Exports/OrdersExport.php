<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class OrdersExport implements
    FromCollection,
    WithHeadings,
    WithStyles,
    WithColumnWidths,
    WithTitle
{
    protected Collection $orders;

    protected array $statusLabel = [
        'pending' => 'Chờ xác nhận',
        'confirmed' => 'Đã xác nhận',
        'shipping' => 'Đang giao',
        'completed' => 'Hoàn thành',
        'cancelled' => 'Đã hủy',
    ];

    public function __construct(Collection $orders)
    {
        $this->orders = $orders;
    }

    public function title(): string
    {
        return 'Đơn hàng';
    }

    public function headings(): array
    {
        return [
            '#',
            'Mã đơn hàng',
            'Người mua',
            'Số điện thoại',
//            'Email',
            'Sản phẩm',
            'Số lượng',
            'Đơn giá (đ)',
            'Tổng tiền (đ)',
//            'Hoàn tiền (đ)',
//            'Trạng thái',
            'Người nhận',
            'SĐT nhận',
//            'Tỉnh/TP',
//            'Quận/Huyện',
//            'Phường/Xã',
            'Địa chỉ ',
            'Ghi chú',
            'Ngày tạo',
        ];
    }

    public function collection(): Collection
    {
        $rows = collect();

        foreach ($this->orders as $o) {
            $details = $o->details;

            // Địa chỉ
//            $tinh = $o->province?->name ?? $o->province_id ?? '';
//            $quan = $o->district?->name ?? $o->district_id ?? '';
//            $phuong = $o->ward?->name ?? $o->ward_id ?? '';


            $tinh = $o->pancakeProvince?->name ?? $o->province_id ?? '';
            $quan = $o->pancakeDistrict?->name ?? $o->district_id ?? '';
            $phuong = $o->pancakeWard?->name ?? $o->ward_id ?? '';

            $dia_chi_ghi = $o->receive_address ?? '';
//            $dia_chi_noi = $tinh . ' - '  . $quan . ' - '  .$phuong . ' - ' . $dia_chi_ghi ;
            $dia_chi_noi = $dia_chi_ghi . ',' . $phuong . ',' . $quan . ',' . $tinh;
            $diaChi = $dia_chi_noi;
            $note = $o->note ?? '';
            $nguoiNhan = $o->receive_user ?? '';
            $sdtNhan = $o->receive_phone ?? '';

            $base = [
                $o->id,
                $o->ma_don_hang,
                $o->user?->name ?? $o->name ?? '',
                $o->user?->tel ?? '',
//                $o->user?->email ?? '',
            ];
            $tail = [
//                $this->statusLabel[$o->status] ?? $o->status,
                $nguoiNhan,
                $sdtNhan,
//                $tinh,
//                $quan,
//                $phuong,
                $diaChi,
                $note,
                $o->created_at?->format('d/m/Y H:i'),
            ];

            if ($details->isEmpty()) {
                $rows->push(array_merge($base, [
                    $o->name ?? '',
                    1,
                    $o->total_price,
                    $o->total_price,
//                    $o->total_price_hoan_tien ?? 0,
                ], $tail));
            } else {
                foreach ($details as $d) {
                    $rows->push(array_merge($base, [
                        $d->name_product,
                        $d->quantity,
                        $d->price_product,
                        $d->price_product * $d->quantity,
//                        $o->total_price_hoan_tien ?? 0,
                    ], $tail));
                }
            }
        }

        return $rows;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,  // #
            'B' => 18,  // Mã đơn
            'C' => 24,  // Người mua
            'D' => 16,  // SĐT
//            'E' => 28,  // Email
            'E' => 30,  // Sản phẩm
            'F' => 8,  // SL
            'G' => 16,  // Đơn giá
            'H' => 16,  // Tổng tiền
//            'J' => 16,  // Hoàn tiền
            'I' => 18,  // Trạng thái
//            'J' => 20,  // Người nhận
            'J' => 14,  // SĐT nhận
//            'N' => 18,  // Tỉnh
//            'O' => 18,  // Quận
//            'P' => 18,  // Phường
            'K' => 36,  // Địa chỉ cụ thể
            'L' => 30,  // Ghi chú
            'M' => 18,  // Ngày tạo
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->getStyle('A1:S1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2563EB']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $sheet->getStyle('H2:J1000')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('H2:J1000')->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('G2:G1000')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->freezePane('A2');

        return [];
    }
}
