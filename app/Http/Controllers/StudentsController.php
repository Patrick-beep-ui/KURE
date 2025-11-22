<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStudentRecordRequest;
use App\Models\Student;
use App\Services\StudentRecordService;
use Illuminate\Http\JsonResponse;
use Exception;
use Illuminate\Http\Request;

class StudentsController extends Controller
{
    protected StudentRecordService $service;

    public function __construct(StudentRecordService $service) {
        $this->service = $service;
    }

    public function getStudents(Request $request)
    {
        try {
            $search = $request->query('search', '');
    
            $students = Student::with([
                'program',
                'contactNumbers' => function($q) {
                    $q->where('type', 'personal');
                },
            ])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'LIKE', "%{$search}%")
                      ->orWhere('last_name', 'LIKE', "%{$search}%")
                      ->orWhere('ku_id', 'LIKE', "%{$search}%")
                      ->orWhere('ku_email', 'LIKE', "%{$search}%")
                      ->orWhereHas('program', function ($sub) use ($search) {
                          $sub->where('program_name', 'LIKE', "%{$search}%");
                      })
                      ->orWhereHas('contactNumbers', function ($sub) use ($search) {
                          $sub->where('phone_number', 'LIKE', "%{$search}%");
                      });
                });
            })
            ->orderBy('first_name')
            ->paginate(10);
    
            return response()->json($students);
    
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Error fetching students',
                'message' => $e->getMessage()
            ], 500);
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
