<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name', 225)->nullable();
            $table->float('price')->default(0);
            $table->float('old_price')->nullable();
            $table->float('price_hoan_tien')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->string('image', 225)->nullable();
            $table->text('image_extra')->nullable();
            $table->string('description', 500)->nullable()->comment('mô tả ngắn');
            $table->text('content')->nullable()->comment('mô tả dài');
            $table->integer('active')->default(1);
            $table->string('slug', 225)->nullable();
            $table->integer('view')->default(0);
            $table->integer('hien_trang_chu')->default(1);
            $table->integer('san_pham_noi_bat')->default(0);
            $table->integer('san_pham_hien_nhu_baner')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->tinyInteger('san_pham_hoan')->default(1)->comment('1 hoặc 0');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
