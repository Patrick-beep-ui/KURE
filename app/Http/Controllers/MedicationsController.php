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

    public function medicationStatus() {
        try {
            $rulesPath = storage_path('app/rules/rules.json');
            $rulesJson = json_decode(file_get_contents($rulesPath), true);

            if (!empty($rulesJson['consultaporrol'])) {
                foreach ($rulesJson['consultaporrol'] as $rule) {
                    $role = trim($rule['rol'], '"');
                    $status = trim($rule['estado'], '"');
                }
            }

            return response()->json(['role' => $role ?? null, 'status' => $status ?? null], 200);
           
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve medication status'], 500);
        }
    }

    public function suggestMedications(Request $request)
    {
        try {
            $reason = $request->get('reason', '');
            if (empty($reason)) {
                return response()->json(['error' => 'Reason is required'], 400);
            }
    
            // Load the rules from JSON
            $rulesPath = storage_path('app/rules/rules.json');
            $rulesJson = json_decode(file_get_contents($rulesPath), true);
    
            $suggestionNames = [];
    
            if (!empty($rulesJson['consultapor'])) {
                foreach ($rulesJson['consultapor'] as $rule) {
                    // simple case-insensitive match on motivo
                    if (stripos($reason, trim($rule['motivo'], '"')) !== false) {
                        foreach ($rule['medicamentos'] as $med) {
                            $suggestionNames[] = trim($med, '"');
                        }
                    }
                }
            }
    
            // Remove duplicates
            $suggestionNames = array_values(array_unique($suggestionNames));
    
            if (empty($suggestionNames)) {
                return response()->json(['suggestions' => []]);
            }
    
            // Fetch matching medications from the database 
            $medications = Medication::whereIn('name', $suggestionNames)->get();
    
            return response()->json(['suggestions' => $medications]);
    
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to get suggestions', 'details' => $e->getMessage()], 500);
        }
    }
    
    

}
