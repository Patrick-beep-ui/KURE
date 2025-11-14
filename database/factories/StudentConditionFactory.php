<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Student;
use App\Models\Condition;

class StudentConditionFactory extends Factory
{
    public function definition()
    {
        return [
            'student_id' => Student::inRandomOrder()->first()->id ?? Student::factory(),
            'condition_id' => Condition::inRandomOrder()->first()->id ?? Condition::factory(),
            'start_date' => $this->faker->date(),
            'end_date'   => null,
            'notes'      => $this->faker->sentence(),
        ];
    }
}
