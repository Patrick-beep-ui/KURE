<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Consultation;
use App\Models\Medication;
use App\Models\ConsultationMedication;

class ConsultationMedicationsSeeder extends Seeder
{
    public function run()
    {
        $consultations = Consultation::all();
        $medications = Medication::all();

        foreach ($consultations as $consultation) {
            ConsultationMedication::factory()->count(1)->create([
                'consultation_id' => $consultation->id,
                'medication_id' => $medications->random()->id
            ]);
        }
    }
}
