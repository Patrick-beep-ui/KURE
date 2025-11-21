<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('consultation_medications', function (Blueprint $table) {
            $table->dropColumn('dosage');
        });
    }

    public function down(): void
    {
        Schema::table('consultation_medications', function (Blueprint $table) {
            $table->string('dosage')->nullable();
        });
    }
};
