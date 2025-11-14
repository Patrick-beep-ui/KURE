<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Student;
use App\Models\User;

class AppointmentFactory extends Factory
{
    public function definition()
    {
        return [
            'student_id' => Student::inRandomOrder()->first()->id ?? Student::factory(),
            'scheduled_by' => User::inRandomOrder()->first()->id ?? User::factory(),
            'consultation_id' => null,
            'date' => $this->faker->date(),
            'start_time' => $this->faker->time(),
            'end_time' => null,
            'status' => 'pendiente',
            'reminder_sent' => false,
        ];
    }
}
