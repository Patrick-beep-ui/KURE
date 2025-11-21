<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStudentRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // you can restrict this later
    }

    public function rules(): array
    {
        return [
            'ku_id' => ['required', 'string', 'unique:students,ku_id'],
            'first_name' => ['required', 'string'],
            'last_name' => ['required', 'string'],
            'ku_email' => ['required', 'email', 'unique:students,ku_email'],
            'dob' => ['required', 'date'],
            'gender' => ['nullable', 'in:male,female,other'],
            'department' => ['nullable', 'string'],
            'address' => ['nullable', 'string'],
            'program_id' => ['nullable', 'exists:programs,id'],
            'residence' => ['nullable', 'in:interno,externo,aquinas'],
    
            'contact_numbers' => ['array'],
            'contact_numbers.*.phone_number' => ['required', 'string'],
            'contact_numbers.*.type' => ['required', 'in:personal,emergencia'],
            'contact_numbers.*.relationship' => ['nullable', 'string'],
    
            'conditions' => ['array'],
            'conditions.*.condition_name' => ['required', 'string'],
            'conditions.*.condition_type' => ['required', 'in:alergia,enfermedad_cronica,lesion,salud_mental,cirugia,otra'],
            'conditions.*.condition_description' => ['nullable', 'string'],
            'conditions.*.start_date' => ['nullable', 'date'],
            'conditions.*.end_date' => ['nullable', 'date'],
            'conditions.*.notes' => ['nullable', 'string'],
    
            'conditions.*.medications' => ['array'],
            'conditions.*.medications.*.medication_id' => ['required', 'exists:medications,id'],
            'conditions.*.medications.*.schedule' => ['nullable', 'string'],
            'conditions.*.medications.*.duration' => ['nullable', 'string'],
        ];
    }
    
}
