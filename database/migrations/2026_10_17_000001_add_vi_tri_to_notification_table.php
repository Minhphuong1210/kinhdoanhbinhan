<?php

use App\Models\FcmUser;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('notification', 'vi_tri')) {
            Schema::table('notification', function (Blueprint $table) {
                // cong_ty | hop_tac_xa — website tạo thông báo, chỉ hiện ở website đó
                $table->string('vi_tri', 50)->nullable();
                $table->index(['target_type', 'target_id', 'vi_tri']);
            });
        }

        // Thông báo cũ thuộc website đang chạy migrate (theo VI_TRI_WEB)
        DB::table('notification')->whereNull('vi_tri')->update(['vi_tri' => FcmUser::defaultViTri()]);
    }

    public function down(): void
    {
        Schema::table('notification', function (Blueprint $table) {
            $table->dropIndex(['target_type', 'target_id', 'vi_tri']);
            $table->dropColumn('vi_tri');
        });
    }
};
