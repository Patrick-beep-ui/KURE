<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AppointmentRequest;
use Illuminate\Http\JsonResponse;
use App\Services\AppointmentService;
use Exception;

class AppointmentsController extends Controller
{
    protected AppointmentService $service;

    public function __construct(AppointmentService $service) {
        $this->service = $service;
    }
    
    public function createAppointment(AppointmentRequest $request): JsonResponse {
        try {
            $appointment = $this->service->scheduleAppointment($request->validated());
            return response()->json([
                'message' => 'Appointment scheduled successfully',
                'data' => $appointment
            ], 201);
        }
        catch(Exception $e) {
            return response()->json([
                'error' => 'Failed to schedule appointment',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
