<?php

namespace App\Services;

use App\Models\Student;
use App\Models\Condition;
use Illuminate\Support\Facades\DB;
use Exception;

class StudentRecordService
{
    public function createStudentRecord(array $data): Student
    {
        try {
            return DB::transaction(function () use ($data) {
    
                $student = Student::create([
                    'ku_id' => $data['ku_id'],
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'ku_email' => $data['ku_email'],
                    'dob' => $data['dob'],
                    'gender' => $data['gender'] ?? null,
                    'department' => $data['department'] ?? null,
                    'address' => $data['address'] ?? null,
                    'program_id' => $data['program_id'] ?? null,
                    'residence' => $data['residence'] ?? null,
                ]);
        
                // Contact numbers
                if (!empty($data['contact_numbers'])) {
                    foreach ($data['contact_numbers'] as $c) {
                        $student->contactNumbers()->create([
                            'phone_number' => $c['phone_number'],
                            'type'         => $c['type'] ?? 'personal',
                            'relationship' => $c['relationship'] ?? null,
                        ]);
                    }
                }

        
                // Conditions
                if (!empty($data['conditions'])) {
                    foreach ($data['conditions'] as $c) {
                        $condition = Condition::firstOrCreate(
                            ['condition_name' => $c['condition_name']],
                            [
                                'condition_type' => $c['condition_type'],
                                'condition_description' => $c['condition_description'] ?? null,
                            ]
                        );
        
                        $studentCondition = $student->conditions()->create([
                            'condition_id' => $condition->id,
                            'start_date' => $c['start_date'] ?? null,
                            'end_date' => $c['end_date'] ?? null,
                            'notes' => $c['notes'] ?? null,
                        ]);
        
                        // Medications
                        if (!empty($c['medications'])) {
                            foreach ($c['medications'] as $m) {
                                $studentCondition->controlledMedications()->create([
                                    'medication_id' => $m['medication_id'],
                                    'schedule' => $m['schedule'] ?? null,
                                    'duration' => $m['duration'] ?? null,
                                ]);
                            }
                        }
                    }
                }
        
                return $student->load(['program', 'contactNumbers', 'conditions.condition', 'conditions.controlledMedications.medication']);
            });
        }
        catch (Exception $e) {
            Db::rollBack();
            throw new Exception('Failed to create student record: ' . $e->getMessage());
        }
    }
    
}
