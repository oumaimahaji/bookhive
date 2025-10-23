<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Use different column names to avoid conflict
        if (!Schema::hasColumn('comments', 'sentiment_analysis')) {
            Schema::table('comments', function (Blueprint $table) {
                $table->enum('sentiment_analysis', ['positive', 'negative', 'neutral'])->nullable();
                $table->decimal('sentiment_score', 3, 2)->nullable()->default(0);
            });
        }

        if (!Schema::hasColumn('posts', 'sentiment_analysis')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->enum('sentiment_analysis', ['positive', 'negative', 'neutral'])->nullable();
                $table->decimal('sentiment_score', 3, 2)->nullable()->default(0);
            });
        }
    }

    public function down()
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropColumn(['sentiment_analysis', 'sentiment_score']);
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn(['sentiment_analysis', 'sentiment_score']);
        });
    }
};