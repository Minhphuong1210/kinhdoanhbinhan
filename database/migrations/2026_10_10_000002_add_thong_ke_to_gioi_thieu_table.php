<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Số liệu hiển thị ở trang giới thiệu, nhập dạng chữ: "10+", "5.000+"
        Schema::table('gioi_thieu', function (Blueprint $table) {
            $table->string('nam_kinh_doanh', 50)->nullable()->after('year');
            $table->string('khach_hang', 50)->nullable()->after('nam_kinh_doanh');
            $table->string('san_pham', 50)->nullable()->after('khach_hang');
        });
    }

    public function down(): void
    {
        Schema::table('gioi_thieu', function (Blueprint $table) {
            $table->dropColumn(['nam_kinh_doanh', 'khach_hang', 'san_pham']);
        });
    }
};
