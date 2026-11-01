<?php

namespace App\Modules\Order\Repositories;

use App\Models\Order;
use App\Modules\Shared\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class OrderRepository extends BaseRepository
{
    protected string $model = Order::class;

    public function paginateOfUser(int $userId): LengthAwarePaginator
    {
        return $this->query()->with('user')->where('user_id', $userId)->latest()->paginate(20)->withQueryString();
    }

    public function paginateForAdmin(array $filters): LengthAwarePaginator
    {
        $search = $filters['search'] ?? null;

        return $this->filtered($this->query()->with('user'), $filters)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('ma_don_hang', 'like', "%$search%")
                        ->orWhere('name', 'like', "%$search%");
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();
    }

    /** Đơn để xuất Excel: admin lọc theo bộ lọc, thành viên chỉ có đơn của mình */
    public function forExport(?int $userId, array $filters): Collection
    {
        $query = $this->query()->with(['user', 'details', 'province', 'district', 'ward']);

        return ($userId ? $query->where('user_id', $userId) : $this->filtered($query, $filters))->get();
    }

    public function findByCode(string $code): Order
    {
        return $this->query()->where('ma_don_hang', $code)->firstOrFail();
    }

    /** Truy vấn danh sách đơn trên API */
    public function forApi(?int $userId, array $filters, string $dateColumn): Builder
    {
        return $this->query()
            ->with(['user:id,name,tel,email', 'details.product.category', 'details.product.coCheHoanPhi'])
            ->when($userId, fn ($query) => $query->where('user_id', $userId))
            ->when($filters['search'] ?? null, function ($query, string $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('ma_don_hang', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                        ->orWhere('receive_user', 'like', "%{$search}%")
                        ->orWhere('receive_phone', 'like', "%{$search}%");
                });
            })
            ->when($filters['status'] ?? null, fn ($query, string $status) => $query->where('status', $status))
            ->when($filters['date_from'] ?? null, fn ($query, string $date) => $query->whereDate($dateColumn, '>=', $date))
            ->when($filters['date_to'] ?? null, fn ($query, string $date) => $query->whereDate($dateColumn, '<=', $date))
            ->when($filters['device'] ?? null, fn ($query, string $device) => $query->where('device', 'like', "%{$device}%"))
            ->when($filters['product_id'] ?? null, function ($query, int $productId) {
                $query->whereHas('details', fn ($q) => $q->where('product_id', $productId));
            });
    }

    private function filtered(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['status'] ?? null, fn ($q, $status) => $q->where('status', $status))
            ->when($filters['product_id'] ?? null, function ($q, $productId) {
                $q->whereHas('details', fn ($detail) => $detail->where('product_id', $productId));
            })
            ->when($filters['date_from'] ?? null, fn ($q, $date) => $q->whereDate('created_at', '>=', $date))
            ->when($filters['date_to'] ?? null, fn ($q, $date) => $q->whereDate('created_at', '<=', $date));
    }
}
