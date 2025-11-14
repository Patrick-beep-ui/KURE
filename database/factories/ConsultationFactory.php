<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Student;

class ConsultationFactory extends Factory
{
    public function definition()
    {
        return [
            'student_id' => Student::inRandomOrder()->first()->id ?? Student::factory(),
            'date' => $this->faker->date(),
            'start_time' => $this->faker->time(),
            'end_time' => $this->faker->time(),
            'reason' => $this->faker->sentence(),
            'diagnosis' => $this->faker->sentence(),
            'notes' => $this->faker->sentence(),
            'provided_by' => User::inRandomOrder()->first()->id ?? User::factory(),
            'type' => $this->faker->randomElement(['doctor','enfermera']),
        ];
    }
}
