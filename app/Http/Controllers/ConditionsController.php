<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConditionForStudentRequest;
use App\Services\ConditionsService;
use App\Models\Condition;
use Illuminate\Http\JsonResponse;
use Exception;
use Illuminate\Http\Request;

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
}
