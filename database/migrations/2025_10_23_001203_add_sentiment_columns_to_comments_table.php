<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSentimentColumnsToCommentsTable extends Migration
{
    public function up()
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->string('sentiment')->nullable()->after('contenu');
            $table->decimal('sentiment_confidence', 5, 4)->nullable()->after('sentiment');
        });
    }

    public function down()
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropColumn(['sentiment', 'sentiment_confidence']);
        });
    }
}
