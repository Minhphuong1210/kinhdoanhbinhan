<?php

namespace App\Modules\DangKyHTX\Repositories;

use App\Models\DangKyXacNhanHtx;
use App\Modules\Shared\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class DangKyHtxRepository extends BaseRepository
{
    protected string $model = DangKyXacNhanHtx::class;

    public function paginateForAdmin(array $filters): LengthAwarePaginator
    {
        $search = $filters['search'] ?? null;

        return $this->query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('full_name', 'like', "%$search%")
                        ->orWhere('phone', 'like', "%$search%");
                });
            })
            ->when(($filters['status'] ?? '') !== '', fn ($q) => $q->where('status', $filters['status']))
            ->latest()
            ->paginate(100)
            ->withQueryString();
    }
}
