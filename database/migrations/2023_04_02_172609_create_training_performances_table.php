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
        Schema::create('training_performances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trainingId')->constrained('trainings')->cascadeOnDelete();
            $table->foreignId('squadMemberId')->constrained('squad_members')->cascadeOnDelete();
            $table->integer('time');
            $table->foreignId('strokeId')->constrained('strokes')->cascadeOnDelete();
            $table->string('rank')->nullable();
            $table->string('points')->nullable();
            $table->string('status')->default('active');
            $table->boolean('isDeleted')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_performances');
    }
};
