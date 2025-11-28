<?php
namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

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
        } catch (\Exception $e) {
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
        return $decoded;
    }

    public function getAstImage()
    {
        $path = $this->dslPath . DIRECTORY_SEPARATOR . 'ast.png';
        if (file_exists($path)) {
            return $path;
        }
        return null;
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
}
