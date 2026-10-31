<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('name', 225)->nullable();
            $table->string('slug', 225)->nullable();
            $table->string('image', 225)->nullable();
            $table->string('description', 225)->nullable();
            $table->text('content')->nullable();
            $table->integer('view')->default(0);
            $table->unsignedBigInteger('category_post_id')->nullable();
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->integer('status')->default(1);
            $table->integer('featured')->default(0);
            $table->string('meta_title', 225)->nullable();
            $table->string('meta_keywords', 225)->nullable();
            $table->string('meta_description', 225)->nullable();
            $table->string('vi_tri_web', 225)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
