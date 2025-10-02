<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evenements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('club_id');
            $table->string('titre');
            $table->text('description')->nullable();
            $table->date('date_event');
            $table->timestamps();

            $table->foreign('club_id')->references('id')->on('club_lectures')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evenements');
    }
};
