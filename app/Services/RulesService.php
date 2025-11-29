<?php
namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use App\Models\Medication;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;

class RulesService
{
    protected $dslPath;
    protected $pythonBin;

    public function __construct()
    {
        $this->dslPath = base_path('dsl');
        $this->pythonBin = env('PYTHON_BIN', 'python'); 
    }

    public function validateRule(string $rule): array
    {
        $rulesFile = $this->dslPath . DIRECTORY_SEPARATOR . 'rules.txt';
        file_put_contents($rulesFile, $rule);

        $cmd = [$this->pythonBin, $this->dslPath . DIRECTORY_SEPARATOR . 'main.py'];

        $process = new Process($cmd);
        $process->setWorkingDirectory($this->dslPath);
        $process->setTimeout(10); // seconds

        try {
            $process->run();
        } catch (Exception $e) {
            return ['ok' => false, 'error' => 'Runtime error: ' . $e->getMessage()];
        }

        $output = $process->getOutput();
        $errorOutput = $process->getErrorOutput();

        if ($errorOutput) {
            // not fatal, but include for debugging
        }

        $decoded = @json_decode($output, true);

        if ($decoded === null) {
            // invalid JSON from Python — surface both outputs for debugging
            $payload = [
                'ok' => false,
                'error' => 'Invalid JSON from Python runtime',
                'output' => $output,
                'stderr' => $errorOutput,
            ];
            return $payload;
        }

        $decoded['ok'] = true;

        $semantic = $this->validateSemantics($decoded['reglas'] ?? []);
        if ($semantic['ok'] === false) {
            return $semantic;
        }

        return $decoded;
    }

    public function getAstImage()
    {
        try {
            $path = $this->dslPath . DIRECTORY_SEPARATOR . 'ast.png';
            if (file_exists($path)) {
                return $path;
            }
            return null;
        }
        catch (Exception $e) {
            return null;
        }
    }

    public function applies(string $eventType, array $context){
        if (!Storage::exists('rules/rules.json')) {
            return false;
        }

        $rules = json_decode(Storage::get('rules/rules.json'), true);

        if ($eventType === "consulta_por") {
            foreach ($rules["consulta_por"] ?? [] as $rule) {
                if (($context["motivo"] ?? '') === ($rule["motivo"] ?? '')) {
                    return true;
                }
            }
        }

        return false;
    }

    public function validateSemantics(array $rules): array
    {
        foreach ($rules as $rule) {
    
            $sentencia = $rule['sentencia'] ?? null;
            if (!$sentencia) continue;
    
            $type = $sentencia['__class__'] ?? null;  
    
            // ---- RULE TYPE: ConsultaPor ----
            if ($type === "ConsultaPor") {
                $result = $this->validateConsultaPor($sentencia);
                if (!$result['ok']) {
                    return $result;
                }
            }
    
            // if ($type === "ConsultaPorRol") ...
            // if ($type === "CondicionTipo") ...
            // etc.
        }
    
        return ['ok' => true];
    }

    private function validateConsultaPor(array $sentencia): array {
        $meds = $sentencia['medicamentos'] ?? [];
    
        $cleanNames = array_map(function ($m) {
            return trim($m, "\"' "); // remove quotes and whitespace
        }, $meds);

        $lowerNames = array_map('strtolower', $cleanNames);
    
        // Fetch all medication names from database
        $existing = Medication::whereIn(DB::raw('LOWER(name)'), $lowerNames)
            ->pluck('name')
            ->toArray();

        $existingLower = array_map('strtolower', $existing);
        $missing = array_diff($lowerNames, $existingLower);
    
        if (!empty($missing)) {
            return [
                'ok' => false,
                'error' => 'Medication not found in database',
                'missing' => array_values($missing),
            ];
        }
    
        return ['ok' => true];
    }

    public function saveRules(array $rules): bool {
        try {
            $path = storage_path('app/rules/rules.json');

            // Load existing rules if file exists
            $existing = [];
            if (file_exists($path)) {
                $existing = json_decode(file_get_contents($path), true) ?? [];
            }
    
            // Merge new rules by type
            foreach ($rules as $rule) {
                $sentencia = $rule['sentencia'] ?? null;
                if (!$sentencia) continue;
    
                $type = $rule['sentencia']['__class__'] ?? 'unknown';
                $key = strtolower($type);
    
                if (!isset($existing[$key])) {
                    $existing[$key] = [];
                }
    
                // Check if the exact same rule exists
                $exists = false;
                foreach ($existing[$key] as $r) {
                    if ($r == $sentencia) {  
                        $exists = true;
                        break;
                    }
                }
    
                if (!$exists) {
                    $existing[$key][] = $rule['sentencia'];
                }
            }
    
            // Save back to file
            return file_put_contents($path, json_encode($existing, JSON_PRETTY_PRINT)) !== false;
        }
        catch (Exception $e) {
            Log::error('Failed to save rules: ' . $e->getMessage());
            return false;
        }
    }

}

