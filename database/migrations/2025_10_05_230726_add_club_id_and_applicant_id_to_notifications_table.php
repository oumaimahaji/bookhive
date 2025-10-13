<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Vérifier si la table notifications existe déjà
        if (!Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Le manager qui reçoit la notification
                $table->foreignId('applicant_id')->constrained('users')->onDelete('cascade'); // L'utilisateur qui fait la demande
                $table->foreignId('club_id')->constrained('club_lectures')->onDelete('cascade'); // Le club concerné
                $table->string('type'); // join_request, join_approved, join_rejected
                $table->text('message');
                $table->string('status')->default('pending'); // pending, accepted, rejected
                $table->timestamp('read_at')->nullable();
                $table->timestamps();
                
                // Index pour améliorer les performances
                $table->index(['user_id', 'status']);
                $table->index(['applicant_id', 'status']);
            });
        } else {
            // Si la table existe déjà, ajouter les colonnes manquantes
            Schema::table('notifications', function (Blueprint $table) {
                if (!Schema::hasColumn('notifications', 'status')) {
                    $table->string('status')->default('pending')->after('message');
                }
                if (!Schema::hasColumn('notifications', 'club_id')) {
                    $table->foreignId('club_id')->nullable()->constrained('club_lectures')->after('user_id');
                }
                if (!Schema::hasColumn('notifications', 'applicant_id')) {
                    $table->foreignId('applicant_id')->nullable()->constrained('users')->after('club_id');
                }
            });
        }
    }

    public function down()
    {
        // Ne pas supprimer la table, seulement les colonnes ajoutées
        Schema::table('notifications', function (Blueprint $table) {
            if (Schema::hasColumn('notifications', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('notifications', 'club_id')) {
                $table->dropForeign(['club_id']);
                $table->dropColumn('club_id');
            }
            if (Schema::hasColumn('notifications', 'applicant_id')) {
                $table->dropForeign(['applicant_id']);
                $table->dropColumn('applicant_id');
            }
        });
    }
};