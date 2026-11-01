<?php

namespace App\Modules\Finance\Repositories;

use App\Models\YeuCauRutTien;
use App\Modules\Shared\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class WithdrawalRepository extends BaseRepository
{
    protected string $model = YeuCauRutTien::class;

    public function paginateForAdmin(array $filters): LengthAwarePaginator
    {
        return $this->filtered($filters)->paginate(100)->withQueryString();
    }

    public function allForExport(array $filters): Collection
    {
        return $this->filtered($filters)->get();
    }

    public function paginateOfUser(int $userId): LengthAwarePaginator
    {
        return $this->query()
            ->with(['nganHang.thanhPho'])
            ->where('user_id', $userId)
            ->latest()
            ->paginate(20);
    }

    /** Khoá yêu cầu trong transaction để không xử lý trùng */
    public function lockedPending(int $id): ?YeuCauRutTien
    {
        return $this->query()->whereKey($id)->where('status', 0)->lockForUpdate()->first();
    }

    private function filtered(array $filters): Builder
    {
        return $this->query()
            ->with(['user', 'nganHang.thanhPho'])
            ->latest()
            ->when(($filters['status'] ?? '') !== '', fn ($q) => $q->where('status', $filters['status']))
            ->when($filters['date_from'] ?? null, fn ($q, $date) => $q->whereDate('created_at', '>=', $date))
            ->when($filters['date_to'] ?? null, fn ($q, $date) => $q->whereDate('created_at', '<=', $date));
    }
}
