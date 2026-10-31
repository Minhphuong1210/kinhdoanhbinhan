<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('dang_ky_xac_nhan_htx', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->string('image', 225)->nullable();
            $table->integer('status')->default(0);
            $table->string('full_name', 225)->nullable();
            $table->date('birthday')->nullable();
            $table->string('gender', 20)->nullable();
            $table->string('nationality', 100)->nullable();
            $table->string('identity_number', 50)->nullable();
            $table->date('identity_date')->nullable();
            $table->string('identity_place', 225)->nullable();
            $table->string('permanent_address', 500)->nullable();
            $table->string('current_address', 500)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email', 225)->nullable();
            $table->string('job', 225)->nullable();
            $table->string('workplace', 225)->nullable();
            $table->string('area', 225)->nullable();
            $table->string('referrer', 225)->nullable();
            $table->string('know_from', 225)->nullable();
            $table->string('know_from_other', 225)->nullable();
            $table->text('goals')->nullable();
            $table->string('goal_other', 225)->nullable();
            $table->text('cccd_files')->nullable();
            $table->string('portrait', 225)->nullable();
            $table->tinyInteger('voluntary')->default(0);
            $table->tinyInteger('understand_model')->default(0);
            $table->tinyInteger('comply_rules')->default(0);
            $table->tinyInteger('correct_communication')->default(0);
            $table->tinyInteger('foundation_lesson')->default(0);
            $table->tinyInteger('data_consent')->default(0);
            $table->text('notify_channels')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });

        Schema::create('leaders', function (Blueprint $table) {
            $table->id();
            $table->string('name', 225)->nullable();
            $table->string('chucvu', 225)->nullable();
            $table->date('ngaysinh')->nullable();
            $table->string('gender', 20)->nullable();
            $table->text('profile')->nullable();
            $table->tinyInteger('ambassador')->default(0);
            $table->string('phone', 20)->nullable();
            $table->string('hoc_van', 225)->nullable();
            $table->text('advantages')->nullable();
            $table->text('progress')->nullable();
            $table->string('years', 50)->nullable();
            $table->string('slogan', 500)->nullable();
            $table->text('achievements')->nullable();
            $table->text('contributions')->nullable();
            $table->text('goals')->nullable();
            $table->text('extra_profile')->nullable();
            $table->text('intro')->nullable();
            $table->integer('status')->default(1);
            $table->integer('order_no')->default(0);
            $table->text('dedication')->nullable();
            $table->tinyInteger('is_ambassador')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leaders');
        Schema::dropIfExists('dang_ky_xac_nhan_htx');
    }
};
