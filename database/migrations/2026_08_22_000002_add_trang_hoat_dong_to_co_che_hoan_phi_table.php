<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('co_che_hoan_phi', 'trang_hoat_dong')) {
            Schema::table('co_che_hoan_phi', function (Blueprint $table) {
                $table->string('trang_hoat_dong', 20)->default('combo')->after('parent_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('co_che_hoan_phi', 'trang_hoat_dong')) {
            Schema::table('co_che_hoan_phi', function (Blueprint $table) {
                $table->dropColumn('trang_hoat_dong');
            });
        }
    }
};
