#!/usr/bin/env php
<?php
/**
 * Manually Process Assignment Submission
 * Run this on production server to manually trigger ProcessSubmissionAudio job
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

if ($argc < 2) {
    echo "Usage: php process_submission_manually.php <submission_id>\n";
    echo "Example: php process_submission_manually.php 33\n";
    exit(1);
}

$submissionId = (int)$argv[1];

echo "================================================\n";
echo "  Manual Submission Processing\n";
echo "  Submission ID: $submissionId\n";
echo "  Date: " . date('Y-m-d H:i:s') . "\n";
echo "================================================\n\n";

// Load submission
$submission = \App\Models\AssignmentSubmission::with(['assignment'])->find($submissionId);

if (!$submission) {
    echo "✗ ERROR: Submission #$submissionId not found\n";
    exit(1);
}

echo "✓ Found submission #$submissionId\n";
echo "  Student ID: {$submission->student_id}\n";
echo "  Assignment: {$submission->assignment->surah} {$submission->assignment->start_verse}";
if ($submission->assignment->end_verse) {
    echo "-{$submission->assignment->end_verse}";
}
echo "\n";
echo "  Status: {$submission->status}\n";
echo "  Audio: {$submission->audio_file_path}\n\n";

// Check if already processed
if ($submission->transcription && $submission->tajweed_analysis) {
    echo "⚠ WARNING: This submission already has transcription and analysis\n";
    echo "  Do you want to reprocess? (y/N): ";
    $handle = fopen("php://stdin", "r");
    $line = fgets($handle);
    if (trim(strtolower($line)) !== 'y') {
        echo "Aborted.\n";
        exit(0);
    }
    fclose($handle);
    echo "\n";
}

// Check audio file exists
$audioPath = storage_path('app/public/' . $submission->audio_file_path);
if (!file_exists($audioPath)) {
    echo "✗ ERROR: Audio file not found: $audioPath\n";
    exit(1);
}

echo "✓ Audio file exists (" . round(filesize($audioPath) / 1024, 2) . " KB)\n\n";

// Dispatch job
echo "Dispatching ProcessSubmissionAudio job...\n";
\App\Jobs\ProcessSubmissionAudio::dispatch($submission->id);
echo "✓ Job dispatched to queue\n\n";

// Wait a moment
echo "Checking if using sync queue...\n";
$queueDriver = config('queue.default');
echo "Queue driver: $queueDriver\n\n";

if ($queueDriver === 'sync') {
    echo "✓ Sync queue detected - job should have run immediately\n";
    echo "Reloading submission...\n";
    $submission->refresh();
    
    if ($submission->transcription) {
        echo "✓ Transcription saved (" . strlen($submission->transcription) . " chars)\n";
        echo "  Preview: " . mb_substr($submission->transcription, 0, 100) . "...\n\n";
    } else {
        echo "✗ No transcription saved\n\n";
    }
    
    if ($submission->tajweed_analysis) {
        $analysis = json_decode($submission->tajweed_analysis, true);
        echo "✓ Tajweed analysis saved\n";
        if (isset($analysis['overall_score']['score'])) {
            echo "  Overall score: {$analysis['overall_score']['score']}%\n";
        }
    } else {
        echo "✗ No Tajweed analysis saved\n";
    }
    
    echo "\nStatus: {$submission->status}\n";
    
} else {
    echo "⚠ Async queue detected ($queueDriver)\n";
    echo "  You need to run: php artisan queue:work\n";
    echo "  Or process jobs manually: php artisan queue:work --once\n\n";
}

// Check logs
echo "\n================================================\n";
echo "  Recent Logs (last 50 lines)\n";
echo "================================================\n";

$logFile = storage_path('logs/laravel.log');
if (file_exists($logFile)) {
    $logContent = file_get_contents($logFile);
    $lines = explode("\n", $logContent);
    $recentLines = array_slice($lines, -50);
    
    $relevantLogs = [];
    foreach ($recentLines as $line) {
        if (strpos($line, "Submission #$submissionId") !== false ||
            strpos($line, 'Processing Audio Job') !== false ||
            strpos($line, 'Python analysis') !== false ||
            strpos($line, 'Whisper transcription') !== false) {
            $relevantLogs[] = $line;
        }
    }
    
    if (!empty($relevantLogs)) {
        foreach ($relevantLogs as $log) {
            echo trim($log) . "\n";
        }
    } else {
        echo "No relevant logs found for this submission\n";
    }
} else {
    echo "Log file not found\n";
}

echo "\n================================================\n";
echo "  DONE\n";
echo "================================================\n";
