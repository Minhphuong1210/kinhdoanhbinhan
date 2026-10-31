<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('chinh_sach', function (Blueprint $table) {
            $table->id();
            $table->string('title', 225)->nullable();
            $table->text('content')->nullable();
            $table->string('url', 500)->nullable();
            $table->string('image', 225)->nullable();
            $table->integer('status')->default(1);
            $table->string('vi_tri_web', 225)->nullable();
            $table->timestamps();
        });

        Schema::create('gioi_thieu', function (Blueprint $table) {
            $table->id();
            $table->string('name', 225)->nullable();
            $table->string('image', 225)->nullable();
            $table->text('content')->nullable();
            $table->string('vi_tri', 225)->nullable();
            $table->string('year', 20)->nullable();
            $table->string('vi_tri_web', 225)->nullable();
            $table->timestamps();
        });

        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('name', 225)->nullable();
            $table->string('slug', 225)->nullable();
            $table->integer('status')->default(1);
            $table->timestamps();
        });

        Schema::create('tag_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tags_id')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tag_products');
        Schema::dropIfExists('tags');
        Schema::dropIfExists('gioi_thieu');
        Schema::dropIfExists('chinh_sach');
    }
};
