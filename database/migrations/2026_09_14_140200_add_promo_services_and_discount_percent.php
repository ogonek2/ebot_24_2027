<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('discounts', function (Blueprint $table) {
            if (!Schema::hasColumn('discounts', 'discount_percent')) {
                $table->unsignedTinyInteger('discount_percent')->nullable()->after('discount_action');
            }
        });

        Schema::create('discount_service', function (Blueprint $table) {
            $table->id();
            $table->foreignId('discount_id')->constrained('discounts')->cascadeOnDelete();
            $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
            $table->string('attach_mode', 16)->default('shared'); // shared|custom|none
            $table->unsignedTinyInteger('custom_percent')->nullable();
            $table->timestamps();
            $table->unique(['discount_id', 'service_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discount_service');

        Schema::table('discounts', function (Blueprint $table) {
            if (Schema::hasColumn('discounts', 'discount_percent')) {
                $table->dropColumn('discount_percent');
            }
        });
    }
};
