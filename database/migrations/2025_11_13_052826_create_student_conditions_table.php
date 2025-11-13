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
        Schema::create('student_conditions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('condition_id')->constrained('conditions')->cascadeOnDelete();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable(); // optional end date for resolved conditions
            $table->text('notes')->nullable(); // extra descriptive details (severity, onset, trigger)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_conditions');
    }
};
