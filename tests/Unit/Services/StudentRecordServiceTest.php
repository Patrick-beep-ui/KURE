<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\StudentRecordService;
use App\Models\Student;
use App\Models\Program;
use App\Models\Condition;
use App\Models\Medication;

class StudentRecordServiceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_creates_student_with_contacts_conditions_and_medications()
    {
        $service = new StudentRecordService();

        $program = Program::factory()->create();

        $med1 = Medication::factory()->create(['name' => 'Med1']);
        $med2 = Medication::factory()->create(['name' => 'Med2']);

        $data = [
            'ku_id' => 'STU1234',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'ku_email' => 'john@example.com',
            'dob' => '2000-01-01',
            'gender' => 'male',
            'department' => 'Managua',
            'address' => 'Some street',
            'program_id' => $program->id,
            'residence' => 'interno',

            'contact_numbers' => [
                ['phone_number' => '12345678', 'type' => 'personal'],
                ['phone_number' => '87654321', 'type' => 'emergencia', 'relationship' => 'Father']
            ],

            'conditions' => [
                [
                    'condition_name' => 'Asthma',
                    'condition_type' => 'enfermedad_cronica',
                    'condition_description' => 'Mild asthma',
                    'start_date' => '2025-01-01',
                    'notes' => 'Uses inhaler',
                    'medications' => [
                        ['medication_id' => $med1->id, 'schedule' => 'Daily', 'duration' => '30 days'],
                        ['medication_id' => $med2->id, 'schedule' => 'Weekly', 'duration' => '2 months'],
                    ],
                ],
                [
                    'condition_name' => 'Allergy',
                    'condition_type' => 'alergia',
                    'start_date' => '2025-02-01',
                    'notes' => 'Peanut allergy',
                ]
            ]
        ];

        $student = $service->createStudentRecord($data);

        $this->assertInstanceOf(Student::class, $student);

        // Student exists
        $this->assertDatabaseHas('students', ['ku_email' => 'john@example.com']);

        // Contact numbers
        $this->assertDatabaseHas('contact_numbers', ['phone_number' => '12345678', 'type' => 'personal']);
        $this->assertDatabaseHas('contact_numbers', ['phone_number' => '87654321', 'type' => 'emergencia', 'relationship' => 'Father']);

        // Conditions
        $this->assertDatabaseHas('conditions', ['condition_name' => 'Asthma']);
        $this->assertDatabaseHas('conditions', ['condition_name' => 'Allergy']);

        // Student conditions
        $this->assertDatabaseHas('student_conditions', ['notes' => 'Uses inhaler']);
        $this->assertDatabaseHas('student_conditions', ['notes' => 'Peanut allergy']);

        // Controlled medications
        $this->assertDatabaseHas('controlled_medications', ['medication_id' => $med1->id, 'schedule' => 'Daily']);
        $this->assertDatabaseHas('controlled_medications', ['medication_id' => $med2->id, 'schedule' => 'Weekly']);
    }
}
