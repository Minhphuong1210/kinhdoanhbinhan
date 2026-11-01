<?php

namespace App\Modules\Order\Services;

use App\Models\Order;
use App\Models\User;

/**
 * Ai được xem, sửa, xoá đơn hàng.
 */
class OrderAccessService
{
    public function canViewAll(User $user): bool
    {
        return $user->is_admin || $user->hasPermission('order.view');
    }

    public function canView(User $user, Order $order): bool
    {
        return (int) $order->user_id === (int) $user->id || $this->canViewAll($user);
    }

    public function canEdit(User $user): bool
    {
        return $user->is_admin || $user->hasPermission('order.edit');
    }

    public function canDelete(User $user): bool
    {
        return $user->is_admin || $user->hasPermission('order.delete');
    }
}
