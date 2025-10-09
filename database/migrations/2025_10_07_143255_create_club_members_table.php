<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('club_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained('club_lectures')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('status')->default('active'); // active, inactive
            $table->timestamp('joined_at')->useCurrent();
            $table->timestamps();
            
            // Empêcher les doublons
            $table->unique(['club_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('club_members');
    }
};