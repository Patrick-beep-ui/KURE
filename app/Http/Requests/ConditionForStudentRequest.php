<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConditionForStudentRequest extends FormRequest
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
            'condition_name' => ['required', 'string'],
            'condition_type' => ['required', 'in:alergia,enfermedad_cronica,lesion,salud_mental,cirugia,otra'],
            'condition_description' => ['nullable', 'string'],
            'diagnosed_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],

            'medications' => ['array'],
            'medications.*.medication_id' => ['required', 'exists:medications,id'],
            'medications.*.schedule' => ['nullable', 'string'],
            'medications.*.duration_type' => ['required', 'in:permanente,fija,prn'],
            'medications.*.duration_unit' => ['nullable', 'in:dias,semanas,meses,años'],
            'medications.*.duration_amount' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
