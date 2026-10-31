<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('co_che_hoan_phi', 'tang_6')) {
            Schema::table('co_che_hoan_phi', function (Blueprint $table) {
                $table->decimal('tang_6', 8, 2)->default(0)->after('tang_5');
            });
        }

        if (!Schema::hasColumn('co_che_hoan_phi', 'parent_id')) {
            Schema::table('co_che_hoan_phi', function (Blueprint $table) {
                $table->unsignedBigInteger('parent_id')->nullable()->after('name');
                $table->index('parent_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('co_che_hoan_phi', 'parent_id')) {
            Schema::table('co_che_hoan_phi', function (Blueprint $table) {
                $table->dropIndex(['parent_id']);
                $table->dropColumn('parent_id');
            });
        }

        if (Schema::hasColumn('co_che_hoan_phi', 'tang_6')) {
            Schema::table('co_che_hoan_phi', function (Blueprint $table) {
                $table->dropColumn('tang_6');
            });
        }
    }
};
