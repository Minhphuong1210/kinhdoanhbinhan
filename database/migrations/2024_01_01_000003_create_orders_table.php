<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('name', 225)->nullable();
            $table->string('ma_don_hang', 225)->nullable();
            $table->string('address', 225)->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('status', 225)->nullable();
            $table->string('note', 225)->nullable();
            $table->string('receive_user', 225)->nullable();
            $table->string('receive_address', 225)->nullable();
            $table->string('receive_phone', 225)->nullable();
            $table->decimal('total_price', 15, 2)->nullable();
            $table->decimal('total_price_hoan_tien', 15, 2)->nullable();
            $table->tinyInteger('first_order')->default(0);
            $table->unsignedBigInteger('province_id')->nullable();
            $table->unsignedBigInteger('district_id')->nullable();
            $table->unsignedBigInteger('ward_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
