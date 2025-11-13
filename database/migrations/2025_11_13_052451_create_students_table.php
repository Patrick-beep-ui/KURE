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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('ku_id')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('ku_email')->unique();
            $table->date('dob');
            $table->string('department')->nullable();
            $table->string('address')->nullable();
            $table->foreignId('program_id')->nullable()->constrained('programs')->nullOnDelete();
            $table->enum('residence', ['interno', 'externo', 'aquinas'])->nullable();
            $table->string('profile_pic_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
