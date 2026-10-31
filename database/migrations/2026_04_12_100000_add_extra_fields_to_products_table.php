<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration 
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $blueprint) {
            $blueprint->longText('instructions')->nullable()->after('content');
            $blueprint->json('licenses')->nullable()->after('instructions');
            $blueprint->text('origin')->nullable()->after('licenses');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $blueprint) {
            $blueprint->dropColumn(['instructions', 'licenses', 'origin']);
        });
    }
};
