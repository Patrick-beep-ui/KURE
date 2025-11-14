<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentsController;

Route::get('/test', function () {
    return response()->json(['message' => 'API is working']);
});


// Students routes
Route::prefix('students')->group(function () {
    Route::get('/', [StudentsController::class, 'getStudents']);
    Route::get('/{id}', [StudentsController::class, 'getStudentById']);
});
