<?php

namespace App\Services;

use App\Models\Consultation;
use App\Models\ConsultationMedication;
use Illuminate\Support\Facades\DB;
use Exception;

class ConsultationService {
    public function createConsultation(array $data): Consultation {
        try {
            DB::beginTransaction();

            $consultation = Consultation::create([
                'student_id' => $data['student_id'],
                'date' => $data['consultation_date'],
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
                'reason' => $data['reason'],
                'diagnosis' => $data['diagnosis'] ?? null,
                'notes' => $data['notes'] ?? null,
                'provided_by' => $data['provided_by'],
                'type' => $data['type'],
            ]);

            if (!empty($data['medications'])) {
                foreach ($data['medications'] as $med) {
                    ConsultationMedication::create([
                        'consultation_id' => $consultation->id,
                        'medication_id' => $med['medication_id'],
                        'quantity' => $med['quantity'],
                        'instructions' => $med['instructions'] ?? null,
                        'status' => $med['status'],
                    ]);
                }
            }

            DB::commit();

            return $consultation;
           
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception('Failed to create consultation: ' . $e->getMessage());
        }
    }
}