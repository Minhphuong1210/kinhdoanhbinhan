<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Mỗi dòng là 1 thiết bị đã đăng nhập app (1 user có thể có nhiều thiết bị)
        Schema::create('fcm_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('fcm', 255);                          // FCM registration token (~160-200 ký tự)
            $table->string('vi_tri', 50)->default('cong_ty');    // cong_ty | hop_tac_xa — app nào nhận thông báo
            $table->string('device_name', 255)->nullable();
            $table->timestamp('last_used_at')->nullable();       // Lần đăng nhập gần nhất bằng token này
            $table->timestamps();

            // 1 token chỉ thuộc 1 user + 1 vị trí; đăng nhập tài khoản khác trên cùng máy thì chuyển chủ
            $table->unique(['fcm', 'vi_tri'], 'fcm_user_fcm_vi_tri_unique');
            $table->index(['user_id', 'vi_tri']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fcm_user');
    }
};
