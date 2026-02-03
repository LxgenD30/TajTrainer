#!/usr/bin/env php
<?php
/**
 * Production Server Diagnostics Script
 * Run this on the hosted server to debug issues
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "================================================\n";
echo "  TajTrainer Production Diagnostics\n";
echo "  Date: " . date('Y-m-d H:i:s') . "\n";
echo "================================================\n\n";

// 1. Check Git Status
echo "=== GIT STATUS ===\n";
$gitLog = shell_exec('git log -1 --oneline');
echo "Current commit: " . $gitLog;
$gitStatus = shell_exec('git status --short');
echo $gitStatus ? "Modified files:\n$gitStatus\n" : "✓ No local modifications\n";
echo "\n";

// 2. Check Python
echo "=== PYTHON ENVIRONMENT ===\n";
$pythonPath = env('PYTHON_PATH', '');
echo "PYTHON_PATH env: " . ($pythonPath ?: 'Not set') . "\n";

$pythonVersion = shell_exec('python3 --version 2>&1');
echo "Python version: " . trim($pythonVersion) . "\n";

$pythonWhich = shell_exec('which python3 2>&1');
echo "Python path: " . trim($pythonWhich) . "\n";

// Test Python analyzer
echo "\nTesting Python analyzer...\n";
$testCmd = 'python3 python/tajweed_analyzer.py --help 2>&1';
$output = shell_exec($testCmd);
if (strpos($output, 'usage:') !== false) {
    echo "✓ Python analyzer accessible\n";
} else {
    echo "✗ Python analyzer error:\n" . substr($output, 0, 200) . "\n";
}
echo "\n";

// 3. Check ProcessSubmissionAudio Code
echo "=== PROCESSSUBMISSIONAUDIO.PHP CODE CHECK ===\n";
$jobFile = file_get_contents('app/Jobs/ProcessSubmissionAudio.php');

if (strpos($jobFile, 'transcribeWithAssemblyAI') !== false) {
    // Check if it's being called in handle()
    $handleMethod = '';
    preg_match('/public function handle\(\).*?\{(.*?)(?=\n    public|\n    private|\Z)/s', $jobFile, $matches);
    if (isset($matches[1])) {
        $handleMethod = $matches[1];
    }
    
    if (strpos($handleMethod, 'transcribeWithAssemblyAI') !== false) {
        echo "✗ OLD CODE: AssemblyAI is still being called in handle() method!\n";
        echo "   Action: Pull latest code from GitHub\n";
    } else {
        echo "✓ Code looks correct (AssemblyAI not called in handle)\n";
    }
} else {
    echo "✗ CRITICAL: AssemblyAI method not found - file might be corrupted\n";
}

// Check for new code markers
if (strpos($jobFile, 'Starting Python analysis (Whisper + Tajweed)') !== false) {
    echo "✓ NEW CODE: Found Python analysis log markers\n";
} else {
    echo "✗ Missing new code markers - might be old version\n";
}
echo "\n";

// 4. Check Recent Submissions
echo "=== RECENT ASSIGNMENT SUBMISSIONS ===\n";
$submissions = \App\Models\AssignmentSubmission::with('assignment')
    ->latest()
    ->take(3)
    ->get();

if ($submissions->isEmpty()) {
    echo "No submissions found\n";
} else {
    foreach ($submissions as $submission) {
        echo "\nSubmission #{$submission->id}\n";
        echo "  Student: {$submission->student_id}\n";
        echo "  Status: {$submission->status}\n";
        echo "  Audio: " . ($submission->audio_file_path ? "Yes ({$submission->audio_file_path})" : "No") . "\n";
        echo "  Transcription: " . (strlen($submission->transcription ?? '') > 0 ? "Yes (" . strlen($submission->transcription) . " chars)" : "No") . "\n";
        echo "  Tajweed Analysis: " . ($submission->tajweed_analysis ? "Yes" : "No") . "\n";
        echo "  Submitted: {$submission->submitted_at}\n";
        
        // Check if audio file exists
        if ($submission->audio_file_path) {
            $fullPath = storage_path('app/public/' . $submission->audio_file_path);
            if (file_exists($fullPath)) {
                $size = filesize($fullPath);
                echo "  Audio file: EXISTS (" . round($size / 1024, 2) . " KB)\n";
            } else {
                echo "  Audio file: MISSING at $fullPath\n";
            }
        }
    }
}
echo "\n";

// 5. Check Laravel Logs (last 50 lines)
echo "=== RECENT LARAVEL LOGS ===\n";
$logFile = storage_path('logs/laravel.log');
if (file_exists($logFile)) {
    $logs = shell_exec("tail -50 '$logFile' 2>&1");
    
    // Look for processing patterns
    if (strpos($logs, 'Processing Audio Job Started') !== false) {
        echo "✓ Found job processing logs\n";
        
        if (strpos($logs, 'Starting Python analysis (Whisper + Tajweed)') !== false) {
            echo "✓ NEW CODE: Python analysis is being called\n";
        } elseif (strpos($logs, 'Transcribing audio with AssemblyAI') !== false) {
            echo "✗ OLD CODE: AssemblyAI is being used!\n";
        }
        
        if (strpos($logs, 'AssemblyAI transcription failed') !== false) {
            echo "✗ AssemblyAI errors detected\n";
        }
        
        if (strpos($logs, 'Whisper transcription:') !== false) {
            echo "✓ Whisper transcriptions found\n";
        }
    } else {
        echo "⚠ No recent job processing logs\n";
    }
    
    echo "\nLast 15 lines of log:\n";
    echo "---\n";
    echo shell_exec("tail -15 '$logFile' 2>&1");
    echo "---\n";
} else {
    echo "✗ Log file not found at $logFile\n";
}
echo "\n";

// 6. Check Storage Permissions
echo "=== STORAGE PERMISSIONS ===\n";
$storageDirs = [
    'storage/app/public/submissions',
    'storage/app/public/practice_recordings',
    'storage/logs',
    'storage/framework/cache',
];

foreach ($storageDirs as $dir) {
    if (is_dir($dir)) {
        $perms = substr(sprintf('%o', fileperms($dir)), -4);
        $writable = is_writable($dir) ? '✓' : '✗';
        echo "$writable $dir (permissions: $perms)\n";
    } else {
        echo "✗ $dir (MISSING)\n";
    }
}
echo "\n";

// 7. Check Configuration
echo "=== CONFIGURATION ===\n";
echo "APP_ENV: " . env('APP_ENV') . "\n";
echo "APP_DEBUG: " . (env('APP_DEBUG') ? 'true' : 'false') . "\n";
echo "APP_URL: " . env('APP_URL') . "\n";
echo "QUEUE_CONNECTION: " . env('QUEUE_CONNECTION') . "\n";
echo "ASSEMBLYAI_API_KEY: " . (env('ASSEMBLYAI_API_KEY') ? 'Set (****)' : 'Not set') . "\n";
echo "\n";

// 8. Summary
echo "================================================\n";
echo "  DIAGNOSTIC SUMMARY\n";
echo "================================================\n";

$issues = [];
$jobFileCheck = file_get_contents('app/Jobs/ProcessSubmissionAudio.php');
preg_match('/public function handle\(\).*?\{(.*?)(?=\n    public|\n    private|\Z)/s', $jobFileCheck, $handleMatches);
$handleContent = $handleMatches[1] ?? '';

if (strpos($handleContent, 'transcribeWithAssemblyAI') !== false) {
    $issues[] = "OLD CODE still in use - AssemblyAI being called";
}

if (strpos($jobFileCheck, 'Starting Python analysis (Whisper + Tajweed)') === false) {
    $issues[] = "Missing new code markers";
}

$recentLog = file_exists($logFile) ? file_get_contents($logFile) : '';
if (strpos($recentLog, 'Transcribing audio with AssemblyAI') !== false) {
    $issues[] = "Logs show AssemblyAI still being used";
}

if (!empty($issues)) {
    echo "✗ ISSUES FOUND:\n";
    foreach ($issues as $issue) {
        echo "  - $issue\n";
    }
    echo "\n";
    echo "RECOMMENDED ACTION:\n";
    echo "1. git fetch origin\n";
    echo "2. git reset --hard origin/main\n";
    echo "3. composer dump-autoload\n";
    echo "4. php artisan cache:clear\n";
    echo "5. php artisan view:clear\n";
    echo "6. php artisan config:clear\n";
    echo "7. Restart PHP-FPM: sudo systemctl restart php8.2-fpm\n";
} else {
    echo "✓ No critical issues detected\n";
    echo "  Code appears to be up to date\n";
}

echo "\n================================================\n";
echo "  End of Diagnostics\n";
echo "================================================\n";
