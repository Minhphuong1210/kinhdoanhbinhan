<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lich_su_giao_dich', function (Blueprint $table) {
            if (!Schema::hasColumn('lich_su_giao_dich', 'product_id')) {
                $table->unsignedBigInteger('product_id')->nullable()->after('company_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('lich_su_giao_dich', function (Blueprint $table) {
            if (Schema::hasColumn('lich_su_giao_dich', 'product_id')) {
                $table->dropColumn('product_id');
            }
        });
    }
};
