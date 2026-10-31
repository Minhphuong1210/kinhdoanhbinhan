<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('setting', function (Blueprint $table) {
            $table->id();
            $table->string('name', 225)->nullable();
            $table->text('value')->nullable();
            $table->string('type', 100)->nullable();
            $table->string('vi_tri_web', 225)->nullable();
            $table->timestamps();
        });

        Schema::create('thu_vien', function (Blueprint $table) {
            $table->id();
            $table->string('name', 225)->nullable();
            $table->string('slug', 225)->nullable();
            $table->string('intro', 500)->nullable();
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->text('content')->nullable();
            $table->string('image', 225)->nullable();
            $table->integer('status')->default(1);
            $table->integer('featured')->default(0);
            $table->string('meta_title', 225)->nullable();
            $table->string('meta_keywords', 225)->nullable();
            $table->string('meta_description', 225)->nullable();
            $table->string('vi_tri_web', 225)->nullable();
            $table->timestamps();
        });

        Schema::create('widgets', function (Blueprint $table) {
            $table->id();
            $table->string('location', 225)->nullable();
            $table->string('title', 225)->nullable();
            $table->string('name', 225)->nullable();
            $table->integer('order_no')->default(0);
            $table->text('content')->nullable();
            $table->integer('status')->default(1);
            $table->string('link', 500)->nullable();
            $table->string('image', 225)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('widgets');
        Schema::dropIfExists('thu_vien');
        Schema::dropIfExists('setting');
    }
};
