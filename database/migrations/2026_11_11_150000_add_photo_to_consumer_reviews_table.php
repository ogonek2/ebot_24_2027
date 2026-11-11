<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPhotoToConsumerReviewsTable extends Migration
{
    public function up()
    {
        Schema::table('consumer_reviews', function (Blueprint $table) {
            $table->string('photo')->nullable()->after('message')->comment('Шлях до фото у storage');
        });
    }

    public function down()
    {
        Schema::table('consumer_reviews', function (Blueprint $table) {
            $table->dropColumn('photo');
        });
    }
}
