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
        Schema::create('controlled_medications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_condition_id')->constrained('student_conditions')->cascadeOnDelete();
            $table->string('schedule')->nullable(); // human readable schedule, e.g. "8 hourly"
            $table->string('duration')->nullable(); // e.g. "5 days"
            $table->foreignId('medication_id')->constrained('medications')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('controlled_medications');
    }
};
