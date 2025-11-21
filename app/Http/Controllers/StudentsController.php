<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStudentRecordRequest;
use App\Models\Student;
use App\Services\StudentRecordService;
use Illuminate\Http\JsonResponse;
use Exception;

class StudentsController extends Controller
{
    protected StudentRecordService $service;

    public function __construct(StudentRecordService $service) {
        $this->service = $service;
    }

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
                'consultations' => function ($query) {
                    $query->orderBy('id', 'desc');
                },
                'consultations.consultationMedications.medication',
                'appointments',
            ])->findOrFail($id);

            return response()->json($student);
        }
        catch(Exception $e) {
            return response()->json(['error' => 'Error fetching student', 'message' => $e->getMessage()], 500);
        }
    }

    public function store(StoreStudentRecordRequest $request): JsonResponse
    {
        try {
            $student = $this->service->createStudentRecord($request->validated());
    
            return response()->json([
                'message' => 'Student record created successfully',
                'data' => $student
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Error creating student record',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function index() {
        try {
            $students = Student::all();

            return response()->json($students);
        }
        catch(Exception $e) {
            return response()->json(['error' => 'Error fetching students', 'message' => $e->getMessage()], 500);
        }
    }
    
}
