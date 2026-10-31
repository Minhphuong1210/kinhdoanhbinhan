<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('purchase_price', 15, 2)->nullable()->after('price');
        });

        $purchasePrices = [
            'combo_1'  => 370000,
            'combo_2'  => 345000,
            'combo_3'  => 345000,
            'combo_4'  => 376000,
            'combo_5'  => 345000,
            'combo_6'  => 345000,
            'combo_7'  => 345000,
            'combo_8'  => 350000,
            'combo_9'  => 350000,
            'combo_10' => 356000,
            'combo_11' => 350000,
            'combo_12' => 0,
            'combo_13' => 0,
            'combo_14' => 0,
            'combo_15' => 0,
            'combo_16' => 330000,
            'combo_21' => 250000,
            'combo_26' => 250000,
            'combo_29' => 225000,
        ];

        foreach ($purchasePrices as $combo => $price) {
            $comboNumber = str_replace('combo_', '', $combo);

            DB::table('products')
                ->where('slug', 'combo-' . $comboNumber)
                ->orWhere('slug', 'like', 'combo-' . $comboNumber . '-%')
                ->update(['purchase_price' => $price]);
        }
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('purchase_price');
        });
    }
};
