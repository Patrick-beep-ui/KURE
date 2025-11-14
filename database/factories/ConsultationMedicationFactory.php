<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Consultation;
use App\Models\Medication;

class ConsultationMedicationFactory extends Factory
{
    public function definition()
    {
        return [
            'consultation_id' => Consultation::inRandomOrder()->first()->id ?? Consultation::factory(),
            'medication_id' => Medication::inRandomOrder()->first()->id ?? Medication::factory(),
            'quantity' => $this->faker->numberBetween(1,4),
            'dosage' => $this->faker->word(),
            'instructions' => $this->faker->sentence(),
            'status' => $this->faker->randomElement(['dada','no_disponible']),
        ];
    }
}
