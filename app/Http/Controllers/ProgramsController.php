<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Program;
use Exception;

class ProgramsController extends Controller
{
    public function getPrograms() {
        try {
            $programs = Program::all();

            return response()->json($programs);

        } catch (Exception $e) {
            return response()->json([
                'error' => 'Error fetching programs',
                'message' => $e->getMessage()
            ], 500);
        }
    }   
}
