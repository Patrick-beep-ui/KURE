<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    public function definition()
    {
        return [
            'first_name' => $this->faker->firstName(),
            'last_name'  => $this->faker->lastName(),
            'ku_id'      => strtoupper($this->faker->bothify('K####')),
            'email'      => $this->faker->unique()->safeEmail(),
            'role'       => $this->faker->randomElement(['doctor', 'enfermera', 'admin']),
        ];
    }
}
