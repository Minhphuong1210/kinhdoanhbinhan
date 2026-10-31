<?php

namespace App\Modules\Payment\Models;

use Illuminate\Database\Eloquent\Model;

class LichSuPayos extends Model
{
    protected $table = 'lich_su_payos';

    protected $fillable = [
        'admin_id',
        'loai_don',
        'hinh_thuc_thanh_toan',
        'so_tien',
        'trang_thai',
        'cancel',
        'link',
        'description',
        'payment_id',
        'ma_don',
        'accountNumber',
        'reference',
        'transactionDateTime',
        'virtualAccountNumber',
        'counterAccountBankId',
        'counterAccountBankName',
        'counterAccountName',
        'counterAccountNumber',
        'virtualAccountName',
        'currency',
        'product_json',
        'order_id'
    ];

    /**
     * Người nạp tiền (user admin)
     */
    public function nguoiNap()
    {
        return $this->belongsTo(\App\Models\User::class, 'admin_id');
    }
}
