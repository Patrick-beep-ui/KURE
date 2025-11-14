<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\Condition;
use App\Models\StudentCondition;

class StudentConditionsSeeder extends Seeder
{
    public function run()
    {
        $students = Student::all();
        $conditions = Condition::all();

        foreach ($students as $student) {
            StudentCondition::factory()->create([
                'student_id' => $student->id,
                'condition_id' => $conditions->random()->id
            ]);
        }
    }
}
