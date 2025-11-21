<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Medication;
use Exception;

class MedicationsController extends Controller
{
    public function index()
    {
        try {
            $medications = Medication::all();
            return response()->json($medications, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve medications'], 500);
        }
    }
}
