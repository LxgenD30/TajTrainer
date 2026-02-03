#!/usr/bin/env php
<?php
/**
 * Check Why Submission Processing Failed
 * This will show the actual error that's preventing processing
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$submissionId = $argc > 1 ? (int)$argv[1] : 33;

echo "================================================\n";
echo "  Checking Submission #$submissionId\n";
echo "  Date: " . date('Y-m-d H:i:s') . "\n";
echo "================================================\n\n";

$submission = \App\Models\AssignmentSubmission::with(['assignment'])->find($submissionId);

if (!$submission) {
    echo "✗ Submission not found\n";
    exit(1);
}

echo "✓ Submission found\n";
echo "  Audio: {$submission->audio_file_path}\n";
echo "  Expected text: {$submission->assignment->expected_recitation}\n\n";

// Check audio file
$audioPath = storage_path('app/public/' . $submission->audio_file_path);
if (!file_exists($audioPath)) {
    echo "✗ ERROR: Audio file not found!\n";
    echo "  Path: $audioPath\n";
    exit(1);
}

echo "✓ Audio file exists: $audioPath\n\n";

// Try to instantiate the job
echo "Creating ProcessSubmissionAudio job instance...\n";
try {
    $job = new \App\Jobs\ProcessSubmissionAudio($submission);
    echo "✓ Job instantiated successfully\n\n";
} catch (\Exception $e) {
    echo "✗ ERROR: Cannot instantiate job\n";
    echo "  Error: {$e->getMessage()}\n";
    echo "  File: {$e->getFile()}:{$e->getLine()}\n";
    exit(1);
}

// Try to execute the job
echo "Executing job handle() method...\n";
echo "This will show any errors during processing:\n";
echo "------------------------------------------------\n";

try {
    $job->handle();
    echo "------------------------------------------------\n";
    echo "✓ Job executed without throwing exception\n\n";
    
    // Reload submission
    $submission->refresh();
    
    echo "Results:\n";
    echo "  Status: {$submission->status}\n";
    echo "  Transcription: " . (strlen($submission->transcription ?? '') > 0 ? "✓ Yes (" . strlen($submission->transcription) . " chars)" : "✗ No") . "\n";
    echo "  Tajweed Analysis: " . ($submission->tajweed_analysis ? "✓ Yes" : "✗ No") . "\n";
    
    if ($submission->transcription) {
        echo "\n  Transcription preview:\n";
        echo "  " . mb_substr($submission->transcription, 0, 200) . "\n";
    }
    
    if ($submission->tajweed_analysis) {
        $analysis = json_decode($submission->tajweed_analysis, true);
        if (isset($analysis['overall_score']['score'])) {
            echo "\n  Overall Score: {$analysis['overall_score']['score']}%\n";
        }
    }
    
} catch (\Exception $e) {
    echo "------------------------------------------------\n";
    echo "✗ ERROR during job execution:\n";
    echo "  Message: {$e->getMessage()}\n";
    echo "  File: {$e->getFile()}:{$e->getLine()}\n";
    echo "\n  Stack trace:\n";
    echo $e->getTraceAsString();
    echo "\n";
}

echo "\n================================================\n";
echo "  Check Recent Logs\n";
echo "================================================\n";

$logFile = storage_path('logs/laravel.log');
if (file_exists($logFile)) {
    $logs = file_get_contents($logFile);
    $lines = explode("\n", $logs);
    $recent = array_slice($lines, -30);
    
    echo "Last 30 log lines:\n";
    echo "------------------------------------------------\n";
    foreach ($recent as $line) {
        if (trim($line)) {
            echo $line . "\n";
        }
    }
}

echo "\n================================================\n";
