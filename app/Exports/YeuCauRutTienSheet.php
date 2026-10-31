<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Cell\StringValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;

class YeuCauRutTienSheet extends StringValueBinder implements
    FromCollection,
    WithHeadings,
    WithStyles,
    WithColumnWidths,
    WithTitle,
    WithCustomValueBinder
{
    protected Collection $items;

    protected array $statusLabel = [
        0 => 'Chờ duyệt',
        1 => 'Đã duyệt',
        2 => 'Từ chối',
    ];

    public function __construct($items)
    {
        $this->items = $items;
    }

    public function title(): string
    {
        return 'Danh sach rut tien';
    }

    public function headings(): array
    {
        return [
            '#',
            'TÊN NV',
            'MÃ NV',
            'CHỨC DANH',
            'SỐ TIỀN',
            'Số tiền cần trả',
            'Mã ngân hàng',
            'Số TK',
            'Số CCCD',
            'Ngày cấp CCCD',
            'Nơi cấp CCCD',
            'Số thẻ',
            'Nội dung giao dịch',
            'Chọn tên ngân hàng chính cần chuyển đến',
            'Chọn chi nhánh của ngân hàng',
            'Chọn Tên chi nhánh ngân hàng cần chuyển đến',
            'Trạng thái',
            'Ngày tạo',
        ];
    }

    public function collection(): Collection
    {
        $rows = collect();

        foreach ($this->items as $i) {


//            dd($i);

            $soTienCanTra = $i->so_tien - $i->so_tien_sau_thue;

            $rows->push([
                $i->id,
                $i->chu_tk ?? '',
                '',
                '',
                $i->so_tien,
                $soTienCanTra,
                $i->nganHang?->id ?? '',
                (string)$i->stk,
                (string)$i->user?->cccd,
                '',
                '',
                '',
                '',
                $i->ngan_hang,
                $i->nganHang?->thanhPho?->name ?? '',
//                $i->user?->chi_nhanh_ngan_hang ?? '',
                $i->nganHang?->name ?? '',
                $this->statusLabel[$i->status] ?? $i->status,
                $i->created_at?->format('d/m/Y H:i'),
            ]);
        }

        return $rows;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,
            'B' => 28,
            'C' => 16,
            'D' => 20,
            'E' => 18,
            'F' => 22,
            'G' => 18,
            'H' => 24,
            'I' => 24,
            'J' => 18,
            'K' => 24,
            'L' => 18,
            'M' => 28,
            'N' => 30,
            'O' => 24,
            'P' => 32,
            'Q' => 18,
            'R' => 22,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $highestRow = $sheet->getHighestRow();

        $sheet->getStyle("A1:R1")->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
                'name' => 'Arial',
            ],

            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1D4ED8'],
            ],

            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],

            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'D1D5DB'],
                ]
            ]
        ]);

        $sheet->getStyle("A2:R{$highestRow}")->applyFromArray([
            'font' => [
                'name' => 'Arial',
                'size' => 10,
            ],

            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],

            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'E5E7EB'],
                ]
            ]
        ]);

        $sheet->getStyle("E2:F{$highestRow}")
            ->getNumberFormat()
            ->setFormatCode('#,##0');

        $sheet->getStyle("A1:R{$highestRow}")
            ->getAlignment()
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->getStyle("A2:A{$highestRow}")
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->getStyle("Q2:Q{$highestRow}")
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->getStyle("B2:P{$highestRow}")
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $sheet->getStyle("E2:F{$highestRow}")
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        $sheet->getRowDimension(1)->setRowHeight(28);

        for ($i = 2; $i <= $highestRow; $i++) {
            $sheet->getRowDimension($i)->setRowHeight(24);
        }

        $sheet->setAutoFilter("A1:R{$highestRow}");

        $sheet->freezePane('A2');

        return [];
    }

    public function bindValue(Cell $cell, $value)
    {
        if ($cell->getRow() == 1) {
            $cell->setValueExplicit($value, DataType::TYPE_STRING);
            return true;
        }

        $column = $cell->getColumn();

        /*
        |--------------------------------------------------------------------------
        | TEXT COLUMN
        |--------------------------------------------------------------------------
        */
        if (in_array($column, [
            'C',
            'G',
            'H',
            'I',
            'J',
            'K',
            'L',
            'M'
        ])) {

            $cell->setValueExplicit((string)$value, DataType::TYPE_STRING);
            return true;
        }

        if (in_array($column, ['E', 'F'])) {

            $cell->setValueExplicit((float)$value, DataType::TYPE_NUMERIC);
            return true;
        }

        return parent::bindValue($cell, $value);
    }

}
