<?php

namespace App\Modules\Finance\Repositories;

use App\Models\LichSuGiaoDich;
use App\Modules\Shared\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TransactionRepository extends BaseRepository
{
    protected string $model = LichSuGiaoDich::class;

    public function paginateForAdmin(?string $search): LengthAwarePaginator
    {
        $query = $this->query()->with(['sender', 'receiver'])->orderByDesc('id');

        return $this->paginateWithSearch($query, $search, 'note', 100);
    }

    public function paginateOfUser(int $userId): LengthAwarePaginator
    {
        return $this->query()
            ->where('admin_id', $userId)
            ->with(['sender', 'receiver'])
            ->orderBy('id', 'desc')
            ->paginate(20);
    }

    public function paginateActivity(int $userId, array $filters): LengthAwarePaginator
    {
        return $this->query()
            ->with([
                'sender:id,name,tel,email',
                'receiver:id,name,tel,email',
                'product:id,name,image',
            ])
            ->where('admin_id', $userId)
            ->when($filters['type'] ?? null, fn ($query, int $type) => $query->where('type', $type))
            ->when($filters['date_from'] ?? null, fn ($query, string $date) => $query->whereDate('created_at', '>=', $date))
            ->when($filters['date_to'] ?? null, fn ($query, string $date) => $query->whereDate('created_at', '<=', $date))
            ->when($filters['search'] ?? null, function ($query, string $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('note', 'like', "%{$search}%")
                        ->orWhereHas('sender', fn ($sender) => $sender->where('name', 'like', "%{$search}%")->orWhere('tel', 'like', "%{$search}%"))
                        ->orWhereHas('receiver', fn ($receiver) => $receiver->where('name', 'like', "%{$search}%")->orWhere('tel', 'like', "%{$search}%"));
                });
            })
            ->orderByDesc('id')
            ->paginate((int) ($filters['per_page'] ?? 20));
    }
}
