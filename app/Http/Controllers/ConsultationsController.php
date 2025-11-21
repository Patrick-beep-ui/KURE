<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConsultationForStudentRequest;
use App\Services\ConsultationService;
use Illuminate\Http\JsonResponse;
use Exception;

class ConsultationsController extends Controller
{
    protected ConsultationService $service;

    public function __construct(ConsultationService $service) {
        $this->service = $service;
    }

    public function createForStudent(ConsultationForStudentRequest $request): JsonResponse
    {
        try {
            $consultation = $this->service->createConsultation($request->validated());
    
            return response()->json([
                'message' => 'Consultation created successfully',
                'data' => $consultation
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Failed to create consultation',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
