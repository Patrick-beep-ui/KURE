<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentsController;
use App\Http\Controllers\ConsultationsController;
use App\Http\Controllers\MedicationsController;
use App\Http\Controllers\ConditionsController;
use App\Http\Controllers\ProgramsController;

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
});

// Conditions Routes
Route::prefix('conditions')->group(function () {
    Route::get('/', [ConditionsController::class, 'getConditions']);
    Route::post('/', [ConditionsController::class, 'createCondition']);
});