<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->string('og_title')->nullable();
            $table->text('og_description')->nullable();
            $table->string('og_image')->nullable();
            $table->string('robots', 120)->nullable();
            $table->string('canonical_path')->nullable();
        });

        Schema::table('services', function (Blueprint $table) {
            $table->string('meta_title')->nullable();
            $table->string('og_title')->nullable();
            $table->text('og_description')->nullable();
            $table->string('og_image')->nullable();
            $table->string('robots', 120)->nullable();
            $table->string('canonical_path')->nullable();
        });

        Schema::table('blog_posts', function (Blueprint $table) {
            $table->string('og_title')->nullable();
            $table->text('og_description')->nullable();
            $table->string('og_image')->nullable();
            $table->string('robots', 120)->nullable();
            $table->string('canonical_path')->nullable();
        });

        Schema::create('seo_pages', function (Blueprint $table) {
            $table->id();
            $table->string('key', 64)->unique();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->string('og_title')->nullable();
            $table->text('og_description')->nullable();
            $table->string('og_image')->nullable();
            $table->string('robots', 64)->nullable();
            $table->string('canonical_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seo_pages');

        Schema::table('blog_posts', function (Blueprint $table) {
            $table->dropColumn(['og_title', 'og_description', 'og_image', 'robots', 'canonical_path']);
        });

        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['meta_title', 'og_title', 'og_description', 'og_image', 'robots', 'canonical_path']);
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn([
                'meta_title', 'meta_description', 'meta_keywords',
                'og_title', 'og_description', 'og_image', 'robots', 'canonical_path',
            ]);
        });
    }
};
