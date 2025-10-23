<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSentimentColumnsToPostsTable extends Migration
{
    public function up()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->string('sentiment')->nullable()->after('image');
            $table->decimal('sentiment_confidence', 5, 4)->nullable()->after('sentiment');
        });
    }

    public function down()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn(['sentiment', 'sentiment_confidence']);
        });
    }
}