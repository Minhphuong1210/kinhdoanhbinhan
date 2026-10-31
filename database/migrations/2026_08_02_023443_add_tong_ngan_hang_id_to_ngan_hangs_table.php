<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ngan_hangs', function (Blueprint $table) {
            $table->unsignedBigInteger('tong_ngan_hang_id')->nullable()->after('thanh_pho_id');
        });
    }

    public function down(): void
    {
        Schema::table('ngan_hangs', function (Blueprint $table) {
            $table->dropColumn('tong_ngan_hang_id');
        });
    }
};
