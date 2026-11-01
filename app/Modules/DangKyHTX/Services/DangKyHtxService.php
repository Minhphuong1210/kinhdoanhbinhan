<?php

namespace App\Modules\DangKyHTX\Services;

use App\Models\DangKyXacNhanHtx;
use App\Modules\DangKyHTX\Repositories\DangKyHtxRepository;
use App\Modules\Shared\Services\BaseService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class DangKyHtxService extends BaseService
{
    public function __construct(DangKyHtxRepository $repository)
    {
        parent::__construct($repository);
    }

    public function list(array $filters): LengthAwarePaginator
    {
        return $this->repository->paginateForAdmin($filters);
    }

    /** PDF chi tiết một đơn đăng ký */
    public function pdf(DangKyXacNhanHtx $dangKyHTX)
    {
        $pdf = app('dompdf.wrapper');
        $pdf->setPaper('a4');
        $pdf->setOptions([
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled' => true,
            'defaultFont' => 'DejaVu Sans',
        ]);

        return $pdf->loadView('DangKyHTX::Admin.pdf', compact('dangKyHTX'));
    }
}
