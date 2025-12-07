<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AppointmentRequest;
use Illuminate\Http\JsonResponse;
use App\Services\AppointmentService;
use App\Models\Appointment;
use App\Models\User;
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

    public function getAppointmentsByStudent($student_id) {
        try {
            $appointments = $this->service->getAppointmentsByStudent($student_id);
            return response()->json($appointments);
        }
        catch(Exception $e) {
            return response()->json([
                'error' => 'Error fetching appointments',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getDoctorAvailability($doctorId)
    {
        try {
            $doctor = User::findOrFail($doctorId);
    
            $rulesPath = storage_path('app/rules/rules.json');
    
            if (!file_exists($rulesPath)) {
                return response()->json([
                    'doctor' => $doctor->first_name . ' ' . $doctor->last_name,
                    'days' => [],
                    'hours' => []
                ]);
            }
    
            $rulesJson = json_decode(file_get_contents($rulesPath), true);
    
            $daysAvailable = [];
            $hoursAvailable = [];
    
            $doctorFullName = strtolower(trim($doctor->first_name . ' ' . $doctor->last_name));
    
            // ✅ Process available days
            foreach ($rulesJson['diasdisponibles'] ?? [] as $rule) {
                if (!isset($rule['doctor'], $rule['dias'])) continue;
    
                $ruleDoctor = strtolower(trim($rule['doctor'], "\"' "));
    
                if ($ruleDoctor === $doctorFullName) {
                    $daysAvailable = array_merge($daysAvailable, $rule['dias']);
                }
            }
    
            // ✅ Process available hours for the specific doctor
            foreach ($rulesJson['horariodisponible'] ?? [] as $rule) {
                if (!isset($rule['doctor'], $rule['horas'])) continue;
    
                $ruleDoctor = strtolower(trim($rule['doctor'], "\"' "));
    
                if ($ruleDoctor === $doctorFullName) {
                    foreach ($rule['horas'] as $hourRange) {
                        if (isset($hourRange['inicio'], $hourRange['fin'])) {
                            $inicio = $hourRange['inicio'];
                            $fin = $hourRange['fin'];
    
                            $hoursAvailable[] = sprintf(
                                "%s %s - %s %s",
                                $inicio['numero'],
                                $inicio['tiempo'],
                                $fin['numero'],
                                $fin['tiempo']
                            );
                        }
                    }
                }
            }
    
            return response()->json([
                'doctor_id' => $doctor->id,
                'doctor' => $doctor->first_name . ' ' . $doctor->last_name,
                'days' => array_values(array_unique($daysAvailable)),
                'hours' => array_values(array_unique($hoursAvailable)),
            ]);
    
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Error fetching doctor availability',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
     
    

}
