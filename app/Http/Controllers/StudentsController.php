<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use Exception;

class StudentsController extends Controller
{
    public function getStudents() {
        try {
            $students = Student::with([
                'program',
                'contactNumbers' => function($query) {
                    $query->where('type', 'personal');
                },
            ])->get();


            return response()->json($students);
        }
        catch(Exception $e) {
            return response()->json(['error' => 'Error fetching students', 'message' => $e->getMessage()], 500);
        }
    }

    public function getStudentById($id) {
        try {
            $student = Student::with([
                'program',
                'contactNumbers',
                'conditions.condition',
                'consultations.consultationMedications.medication',
                'appointments',
            ])->findOrFail($id);

            return response()->json($student);
        }
        catch(Exception $e) {
            return response()->json(['error' => 'Error fetching student', 'message' => $e->getMessage()], 500);
        }
    }
}
