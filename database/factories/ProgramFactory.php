<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProgramFactory extends Factory
{
    public function definition()
    {
        $programName = $this->faker->randomElement([
            'Carrera', 'SEAL', 'Curso de Ingles',
        ]);

        return [
            'program_name' => $programName,
            'modality' => $programName === 'Carrera'
                ? 'diario'
                : $this->faker->randomElement(['sabatino', 'dominical', 'diario', null]),
        ];
    }
}
