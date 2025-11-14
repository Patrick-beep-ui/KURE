<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Medication;

class InventoryFactory extends Factory
{
    public function definition()
    {
        return [
            'medication_id' => Medication::inRandomOrder()->first()->id ?? Medication::factory(),
            'quantity' => $this->faker->numberBetween(5,50),
            'location' => $this->faker->randomElement(['Farmacia','Botiquín 1','Botiquín 2']),
            'batch_number' => strtoupper($this->faker->bothify('BATCH-###')),
            'expiration_date' => $this->faker->date(),
        ];
    }
}
