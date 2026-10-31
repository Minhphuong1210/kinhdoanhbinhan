<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Modify the existing users table to add admin-related fields
        Schema::table('users', function (Blueprint $table) {
            $table->string('tel', 225)->nullable()->after('name');
            $table->string('address', 225)->nullable()->after('tel');
            $table->integer('active')->default(1)->comment('1 kích hoạt, 0 không')->after('address');
            $table->integer('is_admin')->default(0)->after('active');
            $table->unsignedBigInteger('parent_id')->nullable()->after('is_admin');
            $table->float('vi_tien')->nullable()->after('parent_id');
            $table->string('pin', 10)->nullable()->after('vi_tien');
            $table->string('invite_by', 100)->nullable()->after('pin');
            $table->string('image', 225)->nullable()->after('invite_by');
            $table->unsignedBigInteger('province_id')->nullable()->after('image');
            $table->unsignedBigInteger('district_id')->nullable()->after('province_id');
            $table->unsignedBigInteger('ward_id')->nullable()->after('district_id');
            $table->string('ngan_hang', 225)->nullable()->after('ward_id');
            $table->string('stk', 225)->nullable()->after('ngan_hang');
            $table->tinyInteger('vi_tien_status')->default(1)->after('stk');
            $table->string('level', 50)->nullable()->after('vi_tien_status');
            $table->date('ngay_tham_gia')->nullable()->after('level');
            $table->date('ngay_mua_don_cuoi')->nullable()->after('ngay_tham_gia');
            $table->integer('so_thanh_vien_gt')->default(0)->after('ngay_mua_don_cuoi');
            $table->string('fcm', 500)->nullable()->after('so_thanh_vien_gt');
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'tel','address','active','is_admin','parent_id',
                'vi_tien','pin','invite_by','image',
                'province_id','district_id','ward_id',
                'ngan_hang','stk','vi_tien_status','level',
                'ngay_tham_gia','ngay_mua_don_cuoi','so_thanh_vien_gt','fcm','deleted_at'
            ]);
        });
    }
};
