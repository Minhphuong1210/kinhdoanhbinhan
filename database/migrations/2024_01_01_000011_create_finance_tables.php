<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('lich_su_giao_dich', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sender_id')->nullable();
            $table->unsignedBigInteger('receiver_id')->nullable();
            $table->decimal('amount', 15, 2)->nullable();
            $table->string('note', 500)->nullable();
            $table->string('type', 50)->nullable();
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->decimal('surplus', 15, 2)->nullable();
            $table->decimal('hoan_f1', 15, 2)->nullable();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->decimal('surplus_before', 15, 2)->nullable();
            $table->timestamps();
        });

        Schema::create('lich_su_payos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->string('loai_don', 100)->nullable();
            $table->string('hinh_thuc_thanh_toan', 100)->nullable();
            $table->decimal('so_tien', 15, 2)->nullable();
            $table->string('trang_thai', 100)->nullable();
            $table->string('cancel', 50)->nullable();
            $table->string('link', 500)->nullable();
            $table->string('description', 500)->nullable();
            $table->string('payment_id', 225)->nullable();
            $table->string('ma_don', 225)->nullable();
            $table->timestamps();
        });

        Schema::create('yeu_cau_rut_tien', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->decimal('so_tien', 15, 2)->nullable();
            $table->decimal('cast_now', 15, 2)->nullable();
            $table->string('stk', 100)->nullable();
            $table->string('ngan_hang', 225)->nullable();
            $table->string('chu_tk', 225)->nullable();
            $table->tinyInteger('status')->default(0); // 0=chờ, 1=duyệt, 2=từ chối
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('yeu_cau_rut_tien');
        Schema::dropIfExists('lich_su_payos');
        Schema::dropIfExists('lich_su_giao_dich');
    }
};
