<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('co_che_hoan_phi', function (Blueprint $table) {
            $table->id();
            $table->string('name', 225);
            $table->decimal('tang_1', 8, 2)->default(0);
            $table->decimal('tang_2', 8, 2)->default(0);
            $table->decimal('tang_3', 8, 2)->default(0);
            $table->decimal('tang_4', 8, 2)->default(0);
            $table->decimal('tang_5', 8, 2)->default(0);
            $table->timestamps();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->unsignedBigInteger('co_che_hoan_phi_id')->nullable()->after('san_pham_hoan');
            $table->index('co_che_hoan_phi_id');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['co_che_hoan_phi_id']);
            $table->dropColumn('co_che_hoan_phi_id');
        });

        Schema::dropIfExists('co_che_hoan_phi');
    }
};
