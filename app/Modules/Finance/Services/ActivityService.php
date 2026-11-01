<?php

namespace App\Modules\Finance\Services;

use App\Models\LichSuGiaoDich;
use App\Modules\Finance\Repositories\TransactionRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Lịch sử hoạt động của user trên API.
 */
class ActivityService
{
    public function __construct(private readonly TransactionRepository $transactions)
    {
    }

    public function list(int $userId, array $filters): LengthAwarePaginator
    {
        return $this->transactions->paginateActivity($userId, $filters);
    }

    public function payload(LichSuGiaoDich $item): array
    {
        return [
            'id' => $item->id,
            'type' => (int) $item->type,
            'type_label' => $this->typeLabel((int) $item->type),
            'amount' => (float) ($item->amount ?? 0),
            'hoan_f1' => $item->hoan_f1 !== null ? (float) $item->hoan_f1 : null,
            'note' => $item->note,
            'surplus_before' => $item->surplus_before !== null ? (float) $item->surplus_before : null,
            'surplus' => $item->surplus !== null ? (float) $item->surplus : null,
            'surplus_vi_mua_before' => $item->surplus_vi_mua_befor !== null ? (float) $item->surplus_vi_mua_befor : null,
            'surplus_vi_mua' => $item->surplus_vi_mua !== null ? (float) $item->surplus_vi_mua : null,
            'created_at' => $item->created_at?->format('Y-m-d H:i:s'),
            'sender' => $this->userPayload($item->sender),
            'receiver' => $this->userPayload($item->receiver),
            'product' => $item->product ? [
                'id' => $item->product->id,
                'name' => $item->product->name,
                'image' => asset_url($item->product->image),
            ] : null,
        ];
    }

    private function typeLabel(int $type): string
    {
        return match ($type) {
            1 => 'Chi tieu',
            2 => 'Nhan hoa hong',
            3 => 'Rut tien',
            5 => 'Chuyen diem',
            default => 'Hoat dong',
        };
    }

    private function userPayload($user): ?array
    {
        if (!$user) {
            return null;
        }

        return [
            'id' => $user->id,
            'name' => $user->name,
            'tel' => $user->tel,
            'email' => $user->email,
        ];
    }
}
