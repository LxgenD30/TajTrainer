<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\TajweedErrorLog;
use App\Services\ProgressTracker;

echo "=== Testing Progress Tracker Implementation ===\n\n";

// Check table counts
echo "1. Checking table data...\n";
$practiceCount = DB::table('practice_sessions')->count();
$errorLogCount = DB::table('tajweed_error_logs')->count();
$submissionCount = DB::table('assignment_submissions')->count();

echo "   Practice sessions: $practiceCount\n";
echo "   Tajweed error logs: $errorLogCount\n";
echo "   Assignment submissions: $submissionCount\n\n";

// Check if user_id column exists
echo "2. Verifying user_id column removed...\n";
try {
    $columns = DB::select("SHOW COLUMNS FROM tajweed_error_logs");
    $hasUserId = false;
    foreach ($columns as $column) {
        if ($column->Field === 'user_id') {
            $hasUserId = true;
            break;
        }
    }
    echo "   user_id column exists: " . ($hasUserId ? "YES (ERROR!)" : "NO (Good!)") . "\n\n";
} catch (\Exception $e) {
    echo "   Error checking columns: " . $e->getMessage() . "\n\n";
}

// Test a sample user
echo "3. Testing ProgressTracker with user_id = 2...\n";
try {
    $tracker = new ProgressTracker();
    $progress = $tracker->getUserProgress(2, 30);
    
    echo "   Total attempts: " . $progress['total_attempts'] . "\n";
    echo "   Correct count: " . $progress['correct_count'] . "\n";
    echo "   Accuracy: " . $progress['accuracy'] . "%\n";
    echo "   Assignment accuracy: " . $progress['assignment_accuracy'] . "%\n";
    echo "   Practice accuracy: " . $progress['practice_accuracy'] . "%\n\n";
    
    if ($progress['total_attempts'] === 0) {
        echo "   WARNING: No data found for user 2. Query might not be working.\n\n";
    }
} catch (\Exception $e) {
    echo "   ERROR: " . $e->getMessage() . "\n";
    echo "   Stack trace:\n" . $e->getTraceAsString() . "\n\n";
}

// Test raw query
echo "4. Testing raw query for user_id = 2...\n";
try {
    $userId = 2;
    
    // Test practice sessions
    $practiceLogs = DB::table('tajweed_error_logs as tel')
        ->join('practice_sessions as ps', function($join) use ($userId) {
            $join->on('tel.session_id', '=', 'ps.id')
                 ->where('tel.session_type', '=', 'practice');
        })
        ->where('ps.student_id', $userId)
        ->count();
    
    echo "   Practice logs found: $practiceLogs\n";
    
    // Test assignment submissions
    $assignmentLogs = DB::table('tajweed_error_logs as tel')
        ->join('assignment_submissions as asub', function($join) use ($userId) {
            $join->on('tel.session_id', '=', 'asub.id')
                 ->where('tel.session_type', '=', 'assignment');
        })
        ->where('asub.student_id', $userId)
        ->count();
    
    echo "   Assignment logs found: $assignmentLogs\n";
    echo "   Total logs: " . ($practiceLogs + $assignmentLogs) . "\n\n";
    
} catch (\Exception $e) {
    echo "   ERROR: " . $e->getMessage() . "\n\n";
}

// Check sample data
echo "5. Checking sample error log data...\n";
$sampleLog = DB::table('tajweed_error_logs')->first();
if ($sampleLog) {
    echo "   Sample log - session_type: {$sampleLog->session_type}, session_id: {$sampleLog->session_id}\n";
    
    if ($sampleLog->session_type === 'practice') {
        $session = DB::table('practice_sessions')->where('id', $sampleLog->session_id)->first();
        if ($session) {
            echo "   Linked practice session - student_id: {$session->student_id}\n";
        } else {
            echo "   ERROR: Practice session not found!\n";
        }
    } elseif ($sampleLog->session_type === 'assignment') {
        $session = DB::table('assignment_submissions')->where('id', $sampleLog->session_id)->first();
        if ($session) {
            echo "   Linked assignment submission - student_id: {$session->student_id}\n";
        } else {
            echo "   ERROR: Assignment submission not found!\n";
        }
    }
} else {
    echo "   No error logs in database.\n";
}

echo "\n=== Test Complete ===\n";
