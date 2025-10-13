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
            // Ajoute la colonne user_id pour savoir quel utilisateur a ajouté le livre
            $table->unsignedBigInteger('user_id')->nullable()->after('is_valid');

            // Si tu veux, tu peux aussi ajouter une clé étrangère vers la table users
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            // Supprime la colonne user_id si on rollback
            // $table->dropForeign(['user_id']); // décommente si tu as ajouté la clé étrangère
            $table->dropColumn('user_id');
        });
    }
};
