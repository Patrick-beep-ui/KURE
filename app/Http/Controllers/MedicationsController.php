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

    // RulesController.php
    public function suggestMedications(Request $request)
    {
        try {
            $reason = $request->get('reason', '');
            if (empty($reason)) {
                return response()->json(['error' => 'Reason is required'], 400);
            }

            // Load the rules from JSON (you can also cache this)
            $rulesPath = storage_path('app/rules.json');
            $rulesJson = json_decode(file_get_contents($rulesPath), true);

            $suggestions = [];

            if (!empty($rulesJson['consultapor'])) {
                foreach ($rulesJson['consultapor'] as $rule) {
                    // simple case-insensitive match on motivo
                    if (stripos($reason, trim($rule['motivo'], '"')) !== false) {
                        // add all medications for this reason
                        foreach ($rule['medicamentos'] as $med) {
                            $suggestions[] = trim($med, '"');
                        }
                    }
                }
            }

            // Remove duplicates
            $suggestions = array_values(array_unique($suggestions));

            return response()->json(['suggestions' => $suggestions]);

        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to get suggestions', 'details' => $e->getMessage()], 500);
        }
    }

}
