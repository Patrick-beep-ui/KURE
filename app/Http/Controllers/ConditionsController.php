<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConditionForStudentRequest;
use App\Services\ConditionsService;
use App\Models\Condition;
use Illuminate\Http\JsonResponse;
use Exception;
use Illuminate\Http\Request;
use App\Models\Medication;

class ConditionsController extends Controller
{
    protected ConditionsService $service;

    public function __construct(ConditionsService $service) {
        $this->service = $service;
    }

    public function getConditions(Request $request)
    {
        try {
            $search = $request->query('search', '');
    
            $conditions = Condition::with([
                'studentConditions.student'
            ])
            ->whereHas('studentConditions')
            ->when($search, function ($query) use ($search) {
                $query->where('condition_name', 'LIKE', "%{$search}%")
                    ->orWhere('condition_description', 'LIKE', "%{$search}%")
                    ->orWhereHas('studentConditions.student', function ($q) use ($search) {
                        $q->where('first_name', 'LIKE', "%{$search}%")
                          ->orWhere('last_name', 'LIKE', "%{$search}%")
                          ->orWhere('ku_id', 'LIKE', "%{$search}%");
                    });
            })
            ->paginate(10);
    
            return response()->json($conditions);
    
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Error fetching conditions',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    

    public function createCondition(ConditionForStudentRequest $request): JsonResponse {
        try {
            $condition = $this->service->createConditionForStudent($request->validated());
            return response()->json([
                'message' => 'Student record created successfully',
                'data' => $condition
            ], 201);
        }
        catch(Exception $e) {
            return response()->json(['error' => 'Error creating condition', 'message' => $e->getMessage()], 500);
        }
    }

    public function suggestedConditionMedications(Request $request)
    {
        try {
            $condition = $request->get('condition', '');
            if (empty($condition)) {
                return response()->json(['error' => 'Condition is required'], 400);
            }
    
            $rulesPath = storage_path('app/rules/rules.json');
            $rulesJson = json_decode(file_get_contents($rulesPath), true);
    
            $suggestionNames = [];
    
            if (!empty($rulesJson['condicionmedicamentos'])) {
                foreach ($rulesJson['condicionmedicamentos'] as $rule) {
                    // simple case-insensitive match on reason
                    if (stripos($condition, trim($rule['condicion'], '"')) !== false) {
                        foreach ($rule['medicamentos'] as $med) {
                            $suggestionNames[] = trim($med, '"');
                        }
                    }
                }
            }

            $suggestionNames = array_values(array_unique($suggestionNames));
            if (empty($suggestionNames)) {
                return response()->json(['suggestions' => []]);
            }

            $medications = Medication::whereIn('name', $suggestionNames)
            ->get()
            ->map(function ($m) {
                return [
                    'medication_id' => $m->id,
                    'name' => $m->name,
                    'dosage' => $m->dosage,
                    'unit' => $m->unit,
                    'description' => $m->description,
                ];
            });

    
            return response()->json(['suggestions' => $medications], 200);
    
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve condition suggestions'], 500);
        }
    }
}
