<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Bảng sự kiện khuyến mãi theo tháng
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->string('name');                     // Tên sự kiện: "KM tháng 2/2026"
            $table->unsignedTinyInteger('month');       // Tháng: 1-12
            $table->unsignedSmallInteger('year');       // Năm: 2026
            $table->boolean('is_active')->default(1);   // Trạng thái
            $table->text('description')->nullable();    // Mô tả
            $table->timestamps();

            $table->index(['month', 'year', 'is_active']);
        });

        // 2. Bảng liên kết sản phẩm ↔ sự kiện
        Schema::create('product_promotion', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('promotion_id');
            $table->unsignedBigInteger('product_id');   // SP chính
            $table->timestamps();

            $table->foreign('promotion_id')->references('id')->on('promotions')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->unique(['promotion_id', 'product_id']); // Không trùng
        });

        // 3. Bảng quà tặng / khuyến mãi cho mỗi product_promotion
        Schema::create('gifts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_promotion_id');

            // Loại khuyến mãi: 'product' | 'discount_percent' | 'discount_amount'
            $table->enum('type', ['product', 'discount_percent', 'discount_amount'])->default('product');

            // Cho loại 'product': SP quà tặng
            $table->unsignedBigInteger('gift_product_id')->nullable();
            $table->unsignedInteger('quantity')->default(1);

            // Cho loại 'discount_percent' / 'discount_amount': giá trị giảm
            $table->decimal('discount_value', 12, 2)->nullable(); // % hoặc số tiền

            $table->timestamps();

            $table->foreign('product_promotion_id')->references('id')->on('product_promotion')->onDelete('cascade');
            $table->foreign('gift_product_id')->references('id')->on('products')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gifts');
        Schema::dropIfExists('product_promotion');
        Schema::dropIfExists('promotions');
    }
};
