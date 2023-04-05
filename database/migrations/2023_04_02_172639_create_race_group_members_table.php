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
        Schema::create('race_group_members', function (Blueprint $table) {
            $table->id();
            
            $table->string('name');
            $table->string('age');
            $table->foreignId('squadMemberId')->constrained('squad_members')->cascadeOnDelete()->nullable();
            $table->foreignId('raceGroupId')->constrained('race_groups')->cascadeOnDelete();
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
        Schema::dropIfExists('race_group_members');
    }
};
