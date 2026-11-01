<?php

namespace App\Modules\Payment\Repositories;

use App\Models\User;
use App\Modules\Payment\Models\LichSuPayos;
use App\Modules\Shared\Repositories\BaseRepository;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class PaymentRepository extends BaseRepository
{
    protected string $model = LichSuPayos::class;

    /** @return array{0: LengthAwarePaginator, 1: float} [danh sách, tổng tiền thành công] */
    public function paginateWithTotal(array $filters, User $viewer): array
    {
        $query = $this->query()
            ->with('nguoiNap')
            ->latest()
            ->when((int) ($viewer->super_admin ?? 0) !== 1, fn ($q) => $q->where('admin_id', $viewer->id))
            ->when($filters['trang_thai'] ?? null, fn ($q, $v) => $q->where('trang_thai', $v))
            ->when($filters['tu_ngay'] ?? null, fn ($q, $v) => $q->whereDate('created_at', '>=', $v))
            ->when($filters['den_ngay'] ?? null, fn ($q, $v) => $q->whereDate('created_at', '<=', $v))
            ->when($filters['description'] ?? null, fn ($q, $v) => $q->where('description', 'like', '%' . $v . '%'));

        $total = (clone $query)->where('trang_thai', LichSuPayos::STATUS_SUCCESS)->sum('so_tien');

        return [$query->paginate(20)->withQueryString(), $total];
    }

    public function findByOrderCode(int|string $orderCode): ?LichSuPayos
    {
        return $this->query()->where('ma_don', $orderCode)->first();
    }

    public function lockByOrderCode(int|string $orderCode): ?LichSuPayos
    {
        return $this->query()->where('ma_don', $orderCode)->lockForUpdate()->first();
    }

    public function lockPending(int $id): ?LichSuPayos
    {
        return $this->query()->whereKey($id)->where('trang_thai', LichSuPayos::STATUS_PENDING)->lockForUpdate()->first();
    }

    /** Lệnh đang xử lý từ hôm qua đến nay */
    public function recentPending(): Collection
    {
        return $this->query()
            ->where('trang_thai', LichSuPayos::STATUS_PENDING)
            ->whereBetween('created_at', [Carbon::yesterday(), Carbon::now()])
            ->orderByDesc('created_at')
            ->get();
    }
}
