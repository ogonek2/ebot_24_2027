<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            if (!Schema::hasColumn('services', 'sale_price')) {
                $table->unsignedInteger('sale_price')->nullable()->after('individual_price');
            }
            if (!Schema::hasColumn('services', 'individual_sale_price')) {
                $table->unsignedInteger('individual_sale_price')->nullable()->after('sale_price');
            }
            if (!Schema::hasColumn('services', 'sale_source_discount_id')) {
                $table->unsignedBigInteger('sale_source_discount_id')->nullable()->after('individual_sale_price');
            }
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            foreach (['sale_source_discount_id', 'individual_sale_price', 'sale_price'] as $col) {
                if (Schema::hasColumn('services', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
