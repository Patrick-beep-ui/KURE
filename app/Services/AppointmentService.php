<?php 

namespace App\Services;

use App\Models\Appointment;
use Illuminate\Support\Facades\DB;
use Exception;

class AppointmentService {
    public function scheduleAppointment(array $data): Appointment {
        try {
            DB::beginTransaction();

            $appointment = Appointment::create([
                'student_id' => $data['student_id'],
                'date' => $data['date'],
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
                'status' => $data['status'],
                'notes' => $data['notes'] ?? null,
                'scheduled_by' => $data['scheduled_by'],
                'provided_by' => $data['provided_by'],
            ]);

            DB::commit();

            return $appointment;
        }
        catch(Exception $e) {
            DB::rollBack();
            throw new Exception('Failed to schedule appointment: ' . $e->getMessage());
        }
    }

    public function getAppointmentsByStudent($student_id) {
        return Appointment::where('student_id', $student_id)->get();
    }
}