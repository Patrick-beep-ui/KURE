<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentsController;
use App\Http\Controllers\ConsultationsController;
use App\Http\Controllers\MedicationsController;
use App\Http\Controllers\ConditionsController;
use App\Http\Controllers\ProgramsController;
use App\Http\Controllers\RulesController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\AppointmentsController;

Route::get('/test', function () {
    return response()->json(['message' => 'API is working']);
});


// Students routes
Route::prefix('students')->group(function () {
    Route::get('/', [StudentsController::class, 'getStudents']);
    Route::get('/{id}', [StudentsController::class, 'getStudentById']);
    Route::post('/', [StudentsController::class, 'store']);
});

// Programs routes
Route::prefix('programs')->group(function () {
    Route::get('/', [ProgramsController::class, 'getPrograms']);
});


// Consultations routes
Route::prefix('consultations')->group(function() {
    Route::post('/', [ConsultationsController::class, 'createForStudent']);
});

// Medications routes
Route::prefix('medications')->group(function () {
    Route::get('/', [MedicationsController::class, 'index']);
    Route::get('/suggestions', [MedicationsController::class, 'suggestMedications']);
    Route::get('/status', [MedicationsController::class, 'medicationStatus']);
});

// Conditions Routes
Route::prefix('conditions')->group(function () {
    Route::get('/', [ConditionsController::class, 'getConditions']);
    Route::post('/', [ConditionsController::class, 'createCondition']);
    Route::get('/suggestions', [ConditionsController::class, 'suggestedConditionMedications']);
});

//Appointments
Route::prefix('appointments')->group(function () {
    Route::post('/', [AppointmentsController::class, 'createAppointment']);
    Route::get('/student/{student_id}', [AppointmentsController::class, 'getAppointmentsByStudent']);
    Route::get('/doctors', [UsersController::class, 'getDoctors']);
    Route::get('/doctor/availability/{doctor_id}', [AppointmentsController::class, 'getDoctorAvailability']);
});

// Rules DSL routes
Route::prefix('rules')->group(function () {
    Route::post('/validate', [RulesController::class, 'validate']);
    Route::post('/save', [RulesController::class, 'save']);
    Route::get('/ast', [RulesController::class, 'ast']); 
});
