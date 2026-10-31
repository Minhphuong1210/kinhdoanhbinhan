<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const NAME = 'video_gioi_thieu_ve_chung_toi';

    public function up(): void
    {
        // Mỗi website 1 dòng, admin upload ở Cài đặt > tab chung
        foreach (['cong_ty', 'htx'] as $viTriWeb) {
            $exists = DB::table('setting')
                ->where('name', self::NAME)
                ->where('vi_tri_web', $viTriWeb)
                ->exists();

            if (!$exists) {
                DB::table('setting')->insert([
                    'name'       => self::NAME,
                    'value'      => null,
                    'type'       => 'general_tab',
                    'vi_tri_web' => $viTriWeb,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        DB::table('setting')->where('name', self::NAME)->delete();
    }
};
