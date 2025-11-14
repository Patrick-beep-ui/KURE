<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Program;

class StudentFactory extends Factory
{
    public function definition()
    {
        return [
            'ku_id' => strtoupper($this->faker->bothify('S####')),
            'first_name' => $this->faker->firstName(),
            'last_name'  => $this->faker->lastName(),
            'ku_email'   => $this->faker->unique()->safeEmail(),
            'dob'        => $this->faker->date(),
            'department' => $this->faker->randomElement(['Science','Arts','Business']),
            'address'    => $this->faker->address(),
            'program_id' => Program::inRandomOrder()->first()->id ?? Program::factory(),
            'residence'  => $this->faker->randomElement(['interno','externo','aquinas']),
            'profile_pic_url' => null,
        ];
    }
}
