<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ConditionFactory extends Factory
{
    public function definition()
    {
        return [
            'condition_name' => $this->faker->randomElement(['Asthma','Diabetes','Anxiety','Migraines']),
            'condition_type' => $this->faker->randomElement([
                'alergia','enfermedad_cronica','lesion','salud_mental','cirugia','otra'
            ]),
            'condition_description' => $this->faker->sentence(),
        ];
    }
}
