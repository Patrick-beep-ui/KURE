<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Condition;
use Exception;

class ConditionsController extends Controller
{
    public function getConditions() {
        try {
            $conditions = Condition::all();
            return response()->json($conditions);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error fetching conditions', 'message' => $e->getMessage()], 500);
        }
    }
}
