<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ContactNumberFactory extends Factory
{
    public function definition()
    {
        return [
            'phone_number' => $this->faker->numerify('########'),
            'type' => $this->faker->randomElement(['personal','emergencia']),
        ];
    }
}
