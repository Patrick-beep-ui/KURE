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
        Schema::create('consultation_medications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consultation_id')->constrained('consultations')->cascadeOnDelete();
            $table->foreignId('medication_id')->constrained('medications')->cascadeOnDelete();
            $table->integer('quantity')->default(1);
            $table->string('dosage')->nullable();
            $table->text('instructions')->nullable();
            $table->enum('status', ['dada','no_disponible'])->default('dada');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consultation_medications');
    }
};
