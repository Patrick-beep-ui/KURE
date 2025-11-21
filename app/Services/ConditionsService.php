<?php 

namespace App\Services;

use App\Models\Condition;
use App\Models\Medication;
use App\Models\ControlledMedication;
use Illuminate\Support\Facades\DB;
use Exception;

class ConditionsService {
    public function getAllConditionsWithMedications() {
        $conditions = Condition::with(['medications' => function($query) {
            $query->with('controlledMedication');
        }])->get();

        return $conditions;
    }

    public function createConditionForStudent(array $data): Condition {
        try {
            DB::beginTransaction();

            $condition = Condition::firstOrCreate([
                'condition_name' => $data['condition_name'],
                'condition_type' => $data['condition_type'],
                'condition_description' => $data['condition_description'] ?? null,
            ]);

            $studentCondition = $condition->studentConditions()->create([
                'student_id' => $data['student_id'],
                'condition_id' => $condition->id,
                'start_date' => $data['diagnosed_date'] ?? null,
                'end_date' => $data['end_date'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);

            if(!empty($data['medications'])) {
                foreach($data['medications'] as $med) {

                    $studentCondition->controlledMedications()->create([
                        'medication_id' => $med['medication_id'],
                        'schedule' => $med['schedule'],
                        'duration_type' => $med['duration_type'],
                        'duration_amount' => $med['duration_amount'],
                        'duration_unit' => $med['duration_unit'],
                    ]);
                }
            }

            DB::commit();

            return $condition;
        }
        catch(Exception $e) {
            DB::rollBack();
            throw new Exception('Failed to create condition: ' . $e->getMessage());
        }
    }
}