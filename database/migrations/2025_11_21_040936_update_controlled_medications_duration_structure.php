<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('controlled_medications', function (Blueprint $table) {
            $table->dropColumn('duration');

            $table->enum('duration_type', ['permanente', 'fija', 'prn'])
                  ->default('fija');

            $table->integer('duration_amount')
                  ->nullable();

            $table->enum('duration_unit', ['dias', 'semanas', 'meses', 'años'])
                  ->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('controlled_medications', function (Blueprint $table) {
            // Rollback: remove new fields
            $table->dropColumn(['duration_type', 'duration_amount', 'duration_unit']);

            // Restore the original duration column
            $table->string('duration')->nullable();
        });
    }
};

