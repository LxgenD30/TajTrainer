<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== API Keys Status ===\n\n";

$assemblyaiKey = config('services.assemblyai.api_key');
$openaiKey = config('services.openai.api_key');
$pythonExec = config('services.python.executable');

echo "Python Executable: " . ($pythonExec ?: 'python (default)') . "\n";
echo "AssemblyAI Key: " . ($assemblyaiKey ? "SET (" . strlen($assemblyaiKey) . " characters)" : "NOT SET") . "\n";
echo "OpenAI Key: " . ($openaiKey ? "SET (" . strlen($openaiKey) . " characters)" : "NOT SET") . "\n";

if ($assemblyaiKey) {
    echo "\nAssemblyAI Key Format Check:\n";
    echo "  Starts with: " . substr($assemblyaiKey, 0, 10) . "...\n";
    echo "  Contains spaces: " . (strpos($assemblyaiKey, ' ') !== false ? 'YES (ERROR!)' : 'NO (Good)') . "\n";
}

if ($openaiKey) {
    echo "\nOpenAI Key Format Check:\n";
    echo "  Starts with: " . substr($openaiKey, 0, 10) . "...\n";
    echo "  Prefix: " . (str_starts_with($openaiKey, 'sk-') ? 'sk- (Correct)' : 'Wrong format') . "\n";
}

echo "\n";
