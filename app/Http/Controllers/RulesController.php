<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\RulesService;
use Illuminate\Support\Facades\Response;

class RulesController extends Controller
{
    public function validate(Request $request, RulesService $rulesService)
    {
        $rule = $request->get('rule', '');

        if (empty($rule)) {
            return response()->json(['ok' => false, 'error' => 'Empty rule'], 400);
        }

        $result = $rulesService->validateRule($rule);

        if (!isset($result['ok']) || $result['ok'] === false) {
            return response()->json(['error' => $result['error'] ?? 'Unknown Error', 'meta' => $result], 400);
        }

        // semantic validation (database checks)

        return response()->json([
            'ok' => true,
            'ast' => $result['reglas']
        ]);
    }
    
    public function ast(RulesService $rulesService)
    {
        $path = $rulesService->getAstImage();
        if (!$path) {
            return response()->json(['error' => 'AST image not found'], 404);
        }
        return response()->file($path);
    }
}
