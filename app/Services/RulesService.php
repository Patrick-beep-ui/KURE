<?php
namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use App\Models\Medication;
use App\Models\Condition;
use App\Models\User;
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

        $rule = mb_convert_encoding($rule, 'UTF-8', 'UTF-8');
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

        $output = mb_convert_encoding($output, 'UTF-8', 'UTF-8');
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

        if (isset($decoded['reglas'])) {
            foreach ($decoded['reglas'] as &$regla) {
                if (isset($regla['sentencia']['condicion'])) {
                    $regla['sentencia']['condicion'] = str_replace('?', 'ñ', $regla['sentencia']['condicion']);
                }
            }
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

            // ---- RULE TYPE: ConsultaPorRol ----
            if($type === "ConsultaPorRol") {
                $result = $this->validateConsultaPorRol($sentencia);
                if (!$result['ok']) return $result;
            }

            // ---- RULE TYPE: ConsultaPorRol ---- 
            if($type === "CondicionMedicamentos") {
                $result = $this->validateCondicionMedicamentos($sentencia);
                if (!$result['ok']) return $result;
            }

            // ---- RULE TYPE: DiasDisponibles ----
            if($type === "DiasDisponibles") {
                $result = $this->validateDiasDisponibles($sentencia);
                if (!$result['ok']) return $result;
            }

            // ---- RULE TYPE: HorarioDisponible ----
            if($type === "HorarioDisponible") {
                $result = $this->validateHorarioDisponible($sentencia);
                if (!$result['ok']) return $result;
            }
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

    public function validateCondicionMedicamentos(array $sentencia): array {
        $meds = $sentencia['medicamentos'] ?? [];
        $condition = $sentencia['condicion'] ?? null;
    
        // Clean medication names
        $cleanMedNames = array_map(function ($m) {
            return trim($m, "\"' ");
        }, $meds);
    
        // Clean condition name (single string)
        $cleanCondName = trim($condition, "\"' ");
    
        // Lowercase versions
        $lowerMedNames = array_map('strtolower', $cleanMedNames);
        $lowerCondName = strtolower($cleanCondName);
    
        // Fetch existing meds
        $existingMed = Medication::whereIn(DB::raw('LOWER(name)'), $lowerMedNames)
            ->pluck('name')
            ->toArray();
    
        // Fetch existing condition (single lookup)
        $existingCond = Condition::where(DB::raw('LOWER(condition_name)'), $lowerCondName)
            ->pluck('condition_name')
            ->first();
    
        // Normalize existing values
        $existingMedLower = array_map('strtolower', $existingMed);
    
        // Find missing meds
        $missingMeds = array_diff($lowerMedNames, $existingMedLower);
    
        if (!empty($missingMeds)) {
            return [
                'ok' => false,
                'error' => 'Medication not found in database',
                'missing' => array_values($missingMeds),
            ];
        }
    
        // Check condition
        if (!$existingCond) {
            return [
                'ok' => false,
                'error' => 'Condition not found in database',
                'missing' => [$cleanCondName],
            ];
        }
    
        return ['ok' => true];
    }
    

    public function validateConsultaPorRol(array $sentencia): array
    {
        $ROL = strtolower(trim($sentencia['rol'] ?? ''));
        $newEstado = $sentencia['estado'] ?? null;
    
        if (!$ROL || !$newEstado) {
            return [
                'ok' => false,
                'error' => 'se requiren rol y estado de medicamento para esta consulta'
            ];
        }
    
        // Load existing saved rules
        $path = storage_path('app/rules/rules.json');
        $existing = [];
        if (file_exists($path)) {
            $existing = json_decode(file_get_contents($path), true) ?? [];
        }
    
        $existingRules = $existing['consultaporrol'] ?? [];
    
        foreach ($existingRules as $index => $rule) {
            $existingRol = strtolower(trim($rule['rol'] ?? ''));
            $existingEstado = $rule['estado'] ?? null;
    
            if ($existingRol === $ROL) {
    
                // If exactly the same rule, do nothing
                if ($existingEstado === $newEstado) {
                    return ['ok' => true]; 
                }
    
                // If different status, replace the existing rule
                $existing['consultaporrol'][$index]['estado'] = $newEstado;
    
                // Save back
                file_put_contents($path, json_encode($existing, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    
                return [
                    'ok' => true,
                    'replaced' => true,
                    'message' => "Rule for role '{$ROL}' replaced with new status '{$newEstado}'.",
                    'old' => $rule
                ];
            }
        }
    
        // No existing rule for this role → OK
        return ['ok' => true];
    }

    public function validateDiasDisponibles(array $sentencia): array
    {
        $doctorRaw = $sentencia['doctor'] ?? null;
        $dias = $sentencia['dias'] ?? [];
    
        if (!$doctorRaw) {
            return [
                'ok' => false,
                'error' => 'Doctor name is required for available days rule.'
            ];
        }
    
        // Remove quotes
        $doctorFullName = trim($doctorRaw, "\"' ");
    
        // Split into first + last name
        $parts = preg_split('/\s+/', $doctorFullName);
    
        if (count($parts) < 2) {
            return [
                'ok' => false,
                'error' => 'Doctor full name is required (first and last name).',
                'value' => $doctorFullName
            ];
        }
    
        $firstName = strtolower($parts[0]);
        $lastName  = strtolower($parts[count($parts) - 1]); // supports middle names

        $doctor = User::where(DB::raw('LOWER(first_name)'), $firstName)
            ->where(DB::raw('LOWER(last_name)'), $lastName)
            ->first();
    
        if (!$doctor) {
            return [
                'ok' => false,
                'error' => 'Doctor not found in database.',
                'missing' => [$doctorFullName]
            ];
        }
    
        // Ensure the user is actually a doctor
        if (isset($doctor->role) && strtolower($doctor->role) !== 'doctor') {
            return [
                'ok' => false,
                'error' => "User '{$doctorFullName}' exists but is not a doctor."
            ];
        }
    
        // validate day names
        $validDays = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'];
        foreach ($dias as $dia) {
            if (!in_array(strtolower($dia), $validDays)) {
                return [
                    'ok' => false,
                    'error' => "Invalid day detected: {$dia}"
                ];
            }
        }
    
        return ['ok' => true];
    }
    
    public function validateHorarioDisponible(array $sentencia): array {
        $doctorRaw = $sentencia['doctor'] ?? null;
        $dias = $sentencia['dias'] ?? [];
    
        if (!$doctorRaw) {
            return [
                'ok' => false,
                'error' => 'Doctor name is required for available days rule.'
            ];
        }
    
        // Remove quotes
        $doctorFullName = trim($doctorRaw, "\"' ");
    
        // Split into first + last name
        $parts = preg_split('/\s+/', $doctorFullName);
    
        if (count($parts) < 2) {
            return [
                'ok' => false,
                'error' => 'Doctor full name is required (first and last name).',
                'value' => $doctorFullName
            ];
        }
    
        $firstName = strtolower($parts[0]);
        $lastName  = strtolower($parts[count($parts) - 1]); // supports middle names

        $doctor = User::where(DB::raw('LOWER(first_name)'), $firstName)
            ->where(DB::raw('LOWER(last_name)'), $lastName)
            ->first();
    
        if (!$doctor) {
            return [
                'ok' => false,
                'error' => 'Doctor not found in database.',
                'missing' => [$doctorFullName]
            ];
        }
    
        // Ensure the user is actually a doctor
        if (isset($doctor->role) && strtolower($doctor->role) !== 'doctor') {
            return [
                'ok' => false,
                'error' => "User '{$doctorFullName}' exists but is not a doctor."
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
                    $existing[$key][] = $this->utf8ize($rule['sentencia']);
                }
            }
    
            // Save back to file
            return file_put_contents($path, json_encode($existing, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) !== false;
        }
        catch (Exception $e) {
            Log::error('Failed to save rules: ' . $e->getMessage());
            return false;
        }
    }

    private function utf8ize($mixed) {
        if (is_array($mixed)) {
            foreach ($mixed as $key => $value) {
                $mixed[$key] = $this->utf8ize($value);
            }
        } else if (is_string($mixed)) {
            // Convert to UTF-8, ignoring invalid sequences
            return mb_convert_encoding($mixed, 'UTF-8', 'UTF-8'); 
            // or better:
            //return iconv('UTF-8', 'UTF-8//IGNORE', $mixed);
        }
        return $mixed;
    }
    

}

