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
        Schema::create('trainings', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->mediumtext('description')->nullable();
            $table->longtext('requirements')->nullable();
            $table->string('startTime');
            $table->string('endTime');
            $table->string('day');
            $table->string('interval');
            $table->foreignId('squadId')->constrained('squads')->cascadeOnDelete();
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
        Schema::dropIfExists('trainings');
    }
};
