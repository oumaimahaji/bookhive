<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            // Supprime la clé étrangère si elle existe
            $table->dropForeign(['user_id']); 

            // Supprime la colonne user_id
            $table->dropColumn('user_id');    
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            // Recrée la colonne user_id
            $table->unsignedBigInteger('user_id')->nullable();

            // Recrée la clé étrangère
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
