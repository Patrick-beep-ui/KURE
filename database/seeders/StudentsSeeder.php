<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\ContactNumber;

class StudentsSeeder extends Seeder
{
    public function run()
    {
        Student::factory()
            ->count(30)
            ->create()
            ->each(function ($student) {
                ContactNumber::factory()->count(2)->create(['student_id' => $student->id]);
            });
    }
}
