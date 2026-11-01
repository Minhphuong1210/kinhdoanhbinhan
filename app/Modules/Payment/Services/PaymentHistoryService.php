<?php

namespace App\Modules\Payment\Services;

use App\Models\User;
use App\Modules\Payment\Models\LichSuPayos;
use App\Modules\Payment\Repositories\PaymentRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentHistoryService
{
    public function __construct(
        private readonly PayosService $payos,
        private readonly PaymentRepository $payments,
        private readonly PaymentWebhookService $webhookService
    ) {
    }

    /**
     * @return array{0: LengthAwarePaginator, 1: float} [danh sách, tổng tiền thành công]
     */
    public function list(array $filters, User $viewer): array
    {
        return $this->payments->paginateWithTotal($filters, $viewer);
    }

    /**
     * Khách bấm huỷ trên trang PayOS.
     */
    public function cancel(int|string $orderCode, ?string $paymentId): void
    {
        $record = $this->payments->findByOrderCode($orderCode);

        if ($record && !in_array($record->trang_thai, [LichSuPayos::STATUS_SUCCESS, LichSuPayos::STATUS_CANCELLED], true)) {
            $record->update([
                'payment_id' => $paymentId,
                'cancel'     => 1,
                'trang_thai' => LichSuPayos::STATUS_CANCELLED,
            ]);
        }
    }

    /**
     * PayOS redirect về sau khi trả tiền. Chỉ lưu payment_id, cộng tiền do webhook làm.
     *
     * @return bool false nếu không tìm thấy lệnh nạp
     */
    public function markReturned(int|string $orderCode, ?string $paymentId): bool
    {
        $record = $this->payments->findByOrderCode($orderCode);

        if (!$record) {
            return false;
        }

        $record->update(['payment_id' => $paymentId]);

        return true;
    }

    /**
     * Hỏi lại PayOS các lệnh "Đang xử lý" từ hôm qua đến nay (phòng khi webhook bị lỡ).
     *
     * @return int số lệnh được xác nhận thành công
     */
    public function syncPending(): int
    {
        $pending = $this->payments->recentPending();

        $updated = 0;

        foreach ($pending as $item) {
            try {
                $status = $this->payos->getPaymentStatus($item->ma_don);

                if ($status === PayosService::STATUS_PAID && $this->confirmIfStillPending($item)) {
                    $updated++;
                }

                if ($status === PayosService::STATUS_CANCELLED) {
                    $item->update(['trang_thai' => LichSuPayos::STATUS_CANCELLED, 'cancel' => 1]);
                }
            } catch (\Throwable $e) {
                Log::error('[PayOS] syncPending error ma_don=' . $item->ma_don . ': ' . $e->getMessage());
            }
        }

        return $updated;
    }

    private function confirmIfStillPending(LichSuPayos $item): bool
    {
        return DB::transaction(function () use ($item) {
            // Khoá lại để không xử lý trùng với webhook đang chạy song song
            $locked = $this->payments->lockPending($item->id);

            if (!$locked) {
                return false;
            }

            $this->webhookService->confirmPaid($locked, 'Nạp tiền PayOS (sync)');

            return true;
        });
    }
}
