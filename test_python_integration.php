<?php

/**
 * Test Python Integration
 * 
 * This script tests if the Python Tajweed analyzer is properly configured and working.
 * Run this script from the command line: php test_python_integration.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Testing Python Integration ===\n\n";

// Test 1: Check Python executable
echo "1. Testing Python executable...\n";
$pythonExec = config('services.python.executable', 'python');
echo "   Configured executable: $pythonExec\n";

// Try to get Python version
$versionCmd = escapeshellarg($pythonExec) . ' --version 2>&1';
exec($versionCmd, $output, $exitCode);

if ($exitCode === 0) {
    echo "   ✓ Python found: " . implode(' ', $output) . "\n";
} else {
    echo "   ✗ Python not accessible\n";
    echo "   Output: " . implode("\n   ", $output) . "\n";
}

echo "\n2. Testing Python dependencies...\n";
$testCmd = escapeshellarg($pythonExec) . ' -c "import librosa, parselmouth, fastdtw, openai, soundfile; print(\'All dependencies available\')" 2>&1';
exec($testCmd, $depOutput, $depExitCode);

if ($depExitCode === 0) {
    echo "   ✓ All Python dependencies installed\n";
    echo "   Output: " . implode("\n   ", $depOutput) . "\n";
} else {
    echo "   ✗ Missing dependencies\n";
    echo "   Output: " . implode("\n   ", $depOutput) . "\n";
}

echo "\n3. Testing Python analyzer script...\n";
$scriptPath = base_path('python/tajweed_analyzer.py');
if (file_exists($scriptPath)) {
    echo "   ✓ Analyzer script found: $scriptPath\n";
} else {
    echo "   ✗ Analyzer script not found at: $scriptPath\n";
}

echo "\n4. Testing sample audio processing...\n";
// Check if there's any sample audio file
$samplesDir = base_path('python/test_audio_samples');
if (is_dir($samplesDir)) {
    echo "   Test samples directory exists\n";
    $files = array_diff(scandir($samplesDir), ['.', '..']);
    if (count($files) > 0) {
        echo "   Found " . count($files) . " test file(s)\n";
        foreach ($files as $file) {
            echo "   - $file\n";
        }
    }
} else {
    echo "   No test samples directory found\n";
}

echo "\n5. Checking API keys...\n";
$assemblyaiKey = config('services.assemblyai.api_key');
$openaiKey = config('services.openai.api_key');

echo "   AssemblyAI: " . ($assemblyaiKey ? "✓ Configured" : "✗ Not configured") . "\n";
echo "   OpenAI: " . ($openaiKey ? "✓ Configured" : "✗ Not configured") . "\n";

echo "\n6. Recommended .env configuration:\n";
echo "   Add this line to your .env file:\n";
echo "   PYTHON_EXECUTABLE=\"C:\\laragon\\www\\tajtrainerV2\\.venv\\Scripts\\python.exe\"\n";

echo "\n=== Test Complete ===\n";

if ($exitCode === 0 && $depExitCode === 0) {
    echo "\n✓ Python integration appears to be working correctly!\n";
} else {
    echo "\n✗ There are issues with the Python setup. Please fix the errors above.\n";
}
