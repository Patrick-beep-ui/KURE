<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConsultationForStudentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'student_id' => ['required', 'exists:students,id'],
            'provided_by' => ['required', 'exists:users,id'],
            'consultation_date' => ['required', 'date'],
            'type' => ['required', 'in:doctor,enfermera'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'reason' => ['required', 'string'],
            'diagnosis' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],

            'medications' => ['array'],
            'medications.*.medication_id' => ['required', 'exists:medications,id'],
            'medications.*.quantity' => ['required', 'integer', 'min:1'],
            'medications.*.instructions' => ['nullable', 'string'],
            'medications.*.status' => ['required', 'in:dada,no_disponible'],
        ];
    }
}
