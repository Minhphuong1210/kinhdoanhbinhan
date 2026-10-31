<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Giỏ hàng mua tự chọn: mỗi dòng là 1 sản phẩm con thuộc 1 combo (parent_product_id)
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('parent_product_id');      // Combo mua tự chọn
            $table->unsignedBigInteger('product_id');             // Sản phẩm được chọn
            $table->string('mode', 20)->default('custom');        // custom | custom_co_che
            $table->unsignedInteger('quantity')->default(1);
            $table->timestamps();

            $table->foreign('parent_product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->unique(['user_id', 'parent_product_id', 'product_id']);
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
