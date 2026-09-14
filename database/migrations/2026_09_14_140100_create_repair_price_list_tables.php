<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('repair_price_lists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->unique()->constrained('categories')->cascadeOnDelete();
            $table->string('title')->default('Прайс на ремонт');
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        Schema::create('repair_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('repair_price_list_id')->constrained('repair_price_lists')->cascadeOnDelete();
            $table->string('title');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('repair_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('repair_section_id')->constrained('repair_sections')->cascadeOnDelete();
            $table->string('name');
            $table->unsignedInteger('price')->default(0);
            $table->string('price_prefix')->default('від');
            $table->string('note')->nullable();
            $table->string('unit')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repair_items');
        Schema::dropIfExists('repair_sections');
        Schema::dropIfExists('repair_price_lists');
    }
};
