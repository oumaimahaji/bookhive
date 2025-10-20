<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // ajoute la colonne pdf_path
    public function up()
    {
        Schema::table('books', function (Blueprint $table) {
            // chemin du PDF stocké (nullable si pas de PDF)
            $table->string('pdf_path')->nullable();
        });
    }

    // supprime la colonne si rollback
    public function down()
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn('pdf_path');
        });
    }
};
