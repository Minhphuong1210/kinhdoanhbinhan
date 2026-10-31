<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class YeuCauRutTienExport implements WithMultipleSheets
{
    protected $items;

    public function __construct($items)
    {
        $this->items = $items;
    }

    public function sheets(): array
    {
        return [
            new YeuCauRutTienSheet($this->items),
            new ThongKeUserSheet($this->items),
        ];
    }
}