<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Chỉ có ở đơn đặt qua app; đơn web để trống
            $table->string('device', 255)->nullable();     // Thiết bị app gửi lên (vd: "iPhone 15 - iOS 18")
            $table->dateTime('thoi_gian_mua')->nullable();   // Giờ server lúc tạo đơn qua app
            $table->index('thoi_gian_mua');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['thoi_gian_mua']);
            $table->dropColumn(['device', 'thoi_gian_mua']);
        });
    }
};
