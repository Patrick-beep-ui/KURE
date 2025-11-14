<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class MedicationFactory extends Factory
{
    public function definition()
    {
        return [
            'name' => $this->faker->randomElement([
                'Ibuprofeno','Paracetamol','Amoxicilina','Omeprazol','Salbutamol'
            ]),
            'dosage' => $this->faker->randomElement([
                'pastilla','jarabe','pomada','inyeccion','crema','unguento','supositorio','gotas','otro'
            ]),
            'description' => $this->faker->sentence(),
            'unit' => 'mg',
            'notes' => null,
            'stock' => $this->faker->numberBetween(10,200),
            'controlled' => $this->faker->boolean(10), // 10% controlled
        ];
    }
}
