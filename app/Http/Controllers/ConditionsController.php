<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConditionForStudentRequest;
use App\Services\ConditionsService;
use App\Models\Condition;
use Illuminate\Http\JsonResponse;
use Exception;

class ConditionsController extends Controller
{
    protected ConditionsService $service;

    public function __construct(ConditionsService $service) {
        $this->service = $service;
    }

    public function getConditions() {
        try {
            $conditions = Condition::all();
            return response()->json($conditions);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error fetching conditions', 'message' => $e->getMessage()], 500);
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
