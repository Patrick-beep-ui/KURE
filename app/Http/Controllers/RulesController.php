<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\RulesService;
use Illuminate\Support\Facades\Response;
use Exception;

class RulesController extends Controller
{
    public function validate(Request $request, RulesService $rulesService, bool $save = false)
    {
        try {
            $rule = $request->get('rule', '');

            if (empty($rule)) {
                return response()->json(['ok' => false, 'error' => 'Empty rule'], 400);
            }
    
            $result = $rulesService->validateRule($rule);
    
            if (!isset($result['ok']) || $result['ok'] === false) {
                return response()->json(['error' => $result['error'] ?? 'Unknown Error', 'meta' => $result], 400);
            }
    
            // save rules once validated
            if($save) {
                $saved = $rulesService->saveRules($result['reglas']);
                if (!$saved) {
                    return response()->json(['ok' => false, 'error' => 'Failed to save rules'], 500);
                }
            }
    
            return response()->json([
                'ok' => true,
                'ast' => $result['reglas']
            ]);
        }
        catch (Exception $e) {
            return response()->json(['error' => 'Failed to validate rule', 'details' => $e->getMessage()], 500);
        }
    }

    public function ast(RulesService $rulesService)
    {
        try {
            $path = $rulesService->getAstImage();
            if (!$path) {
                return response()->json(['error' => 'AST image not found'], 404);
            }
            return response()->file($path);
        }
        catch (Exception $e) {
            return response()->json(['error' => 'Failed to generate AST image', 'details' => $e->getMessage()], 500);
        }
    }

    public function save(Request $request) {
        try {
            return $this->validate($request, app(RulesService::class), true);
        }
        catch( Exception $e) {
         return response()->json(['error' => 'Failed to save rule', 'details' => $e->getMessage()], 500);   
        }
    }
}
