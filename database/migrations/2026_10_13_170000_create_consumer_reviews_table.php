<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConsumerReviewsTable extends Migration
{
    public function up()
    {
        Schema::create('consumer_reviews', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Ім\'я клієнта');
            $table->string('phone', 40)->comment('Телефон');
            $table->string('email')->nullable()->comment('Email');
            $table->string('order_number')->nullable()->comment('Номер замовлення');
            $table->string('type', 40)->default('review')->comment('review|claim|suggestion');
            $table->text('message')->comment('Текст звернення / відгуку');
            $table->string('status', 40)->default('new')->comment('new|in_progress|done|archived');
            $table->text('admin_notes')->nullable()->comment('Нотатки адміністратора');
            $table->timestamps();

            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('consumer_reviews');
    }
}
