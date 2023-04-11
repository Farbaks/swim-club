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
        Schema::create('race_performances', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('age');
            $table->string('club')->nullable();
            $table->foreignId('userId')->constrained('users')->cascadeOnDelete()->nullable();
            $table->foreignId('raceGroupId')->constrained('race_groups')->cascadeOnDelete();
            
            $table->string('time')->nullable();
            $table->foreignId('strokeId')->constrained('strokes')->cascadeOnDelete()->nullable();
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
        Schema::dropIfExists('race_performances');
    }
};
