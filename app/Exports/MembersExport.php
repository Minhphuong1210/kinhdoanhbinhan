<?php

namespace App\Exports;

use App\Models\User;
use App\Models\LichSuGiaoDich;
use App\Models\Order;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Carbon\Carbon;

class MembersExport implements
    FromCollection,
    WithHeadings,
    WithStyles,
    WithColumnWidths,
    WithTitle
{
    protected ?string $startDate;
    protected ?string $endDate;

    public function __construct(?string $startDate = null, ?string $endDate = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function title(): string
    {
        return 'Danh sách thành viên';
    }

    public function headings(): array
    {
        return [
            'ID',
            'Họ tên',
            'Email',
            'Số điện thoại',

            'Ngày sinh',
            'Giới tính',
            'Quốc tịch',
            'Số CCCD',
            'Ngày cấp CCCD',
            'Địa khẩu thường chú',
            'Người giới thiệu',
            'Cấp trên (Cha)',
            'Số dư hiện tại (đ)',
            'Hoa hồng trong kỳ (đ)',
            'Tiền mua hàng trong kỳ (đ)',
            'Ngày tham gia',
        ];
    }

    public function collection(): Collection
    {
        $query = User::query()->with([
            'inviteBy',
            'parent',
            'dangKyXacNhanHtx',
        ]);

        if ($this->startDate) {
            $query->whereDate('created_at', '>=', $this->startDate);
        }
        if ($this->endDate) {
            $query->whereDate('created_at', '<=', $this->endDate);
        }

        $users = $query->get();
        $rows = collect();

        // Prepare range for sub-queries
        $start = $this->startDate ? Carbon::parse($this->startDate)->startOfDay() : null;
        $end = $this->endDate ? Carbon::parse($this->endDate)->endOfDay() : null;

        $commissions = LichSuGiaoDich::query()
            ->where('type', 2)
            ->when($start, function ($q) use ($start) {
                $q->where('created_at', '>=', $start);
            })
            ->when($end, function ($q) use ($end) {
                $q->where('created_at', '<=', $end);
            })
            ->selectRaw('admin_id, SUM(amount) as total')
            ->groupBy('admin_id')
            ->pluck('total', 'admin_id');

        $orders = Order::query()
            ->where('status', 'completed')
            ->when($start, function ($q) use ($start) {
                $q->where('created_at', '>=', $start);
            })
            ->when($end, function ($q) use ($end) {
                $q->where('created_at', '<=', $end);
            })
            ->selectRaw('user_id, SUM(total_price) as total')
            ->groupBy('user_id')
            ->pluck('total', 'user_id');

        foreach ($users as $u) {

            $htx = $u->dangKyXacNhanHtx;

            $rows->push([
                $u->id,
                $u->name,
                $u->email,
                $u->tel ?? '',

                $htx?->birthday
                    ? Carbon::parse($htx->birthday)->format('d/m/Y')
                    : '',

                $htx?->gender ?? '',
                $htx?->nationality ?? '',
                $htx?->identity_number ?? '',

                $htx?->identity_date
                    ? Carbon::parse($htx->identity_date)->format('d/m/Y')
                    : '',


                $htx?->permanent_address ?? '',

                $u->inviteBy?->name ?? '—',
                $u->parent?->name ?? '—',

                $u->vi_tien,

                $commissions[$u->id] ?? 0,

                $orders[$u->id] ?? 0,

                $u->created_at?->format('d/m/Y H:i'),
            ]);
        }

        return $rows;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10,
            'B' => 25,
            'C' => 30,
            'D' => 15,

            'E' => 15,
            'F' => 12,
            'G' => 15,
            'H' => 20,
            'I' => 15,

            'J' => 25,
            'K' => 25,

            'L' => 18,
            'M' => 18,
            'N' => 18,
            'O' => 20,

            'P'=>20,
        ];


    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->getStyle('A1:O1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '10B981'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        $sheet->getStyle('M2:O1000')
            ->getNumberFormat()
            ->setFormatCode('#,##0');

        $sheet->getStyle('M2:O1000')
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        return [];
    }
}
