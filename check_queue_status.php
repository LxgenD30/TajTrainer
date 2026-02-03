#!/usr/bin/env php
<?php
/**
 * Check Queue Status on Production Server
 * Diagnose why submissions aren't being processed automatically
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "================================================\n";
echo "  Queue System Diagnostics\n";
echo "  Date: " . date('Y-m-d H:i:s') . "\n";
echo "================================================\n\n";

// 1. Check queue configuration
echo "=== QUEUE CONFIGURATION ===\n";
$queueDriver = config('queue.default');
$queueConnection = config("queue.connections.{$queueDriver}");

echo "Default Driver: $queueDriver\n";
if ($queueConnection) {
    echo "Connection Config:\n";
    foreach ($queueConnection as $key => $value) {
        if (!is_array($value)) {
            echo "  $key: $value\n";
        }
    }
} else {
    echo "⚠ No connection config found for driver: $queueDriver\n";
}
echo "\n";

// 2. Check if queue workers are running
echo "=== QUEUE WORKERS ===\n";
$processes = shell_exec('ps aux | grep "queue:work" | grep -v grep');
if ($processes) {
    echo "✓ Queue workers found:\n";
    echo $processes;
} else {
    echo "✗ No queue workers running\n";
    echo "  To start: php artisan queue:work\n";
    echo "  Or use supervisor for persistent workers\n";
}
echo "\n";

// 3. Check pending jobs
echo "=== PENDING JOBS ===\n";
try {
    if ($queueDriver === 'database') {
        $pendingJobs = \DB::table('jobs')->count();
        $failedJobs = \DB::table('failed_jobs')->count();
        
        echo "Pending jobs: $pendingJobs\n";
        echo "Failed jobs: $failedJobs\n\n";
        
        if ($pendingJobs > 0) {
            echo "Latest pending jobs:\n";
            $jobs = \DB::table('jobs')->latest()->take(5)->get();
            foreach ($jobs as $job) {
                $payload = json_decode($job->payload, true);
                $jobClass = $payload['displayName'] ?? 'Unknown';
                echo "  - Job #$job->id: $jobClass (Attempts: $job->attempts)\n";
                echo "    Created: " . date('Y-m-d H:i:s', $job->created_at) . "\n";
            }
        }
        
        if ($failedJobs > 0) {
            echo "\nLatest failed jobs:\n";
            $failed = \DB::table('failed_jobs')->latest()->take(3)->get();
            foreach ($failed as $job) {
                $payload = json_decode($job->payload, true);
                $jobClass = $payload['displayName'] ?? 'Unknown';
                echo "  - Failed Job #$job->id: $jobClass\n";
                echo "    Failed at: $job->failed_at\n";
                echo "    Exception: " . substr($job->exception, 0, 200) . "...\n\n";
            }
        }
        
    } else {
        echo "⚠ Queue driver is '$queueDriver' (not database)\n";
        echo "  Cannot check pending jobs count\n";
        
        if ($queueDriver === 'sync') {
            echo "\n✓ SYNC queue means jobs run immediately (no queue)\n";
            echo "  Jobs should execute as soon as they're dispatched\n";
        }
    }
} catch (\Exception $e) {
    echo "✗ Error checking jobs: {$e->getMessage()}\n";
}
echo "\n";

// 4. Check recent submissions that should have been processed
echo "=== RECENT UNPROCESSED SUBMISSIONS ===\n";
$unprocessed = \App\Models\AssignmentSubmission::whereNull('transcription')
    ->orWhereNull('tajweed_analysis')
    ->latest()
    ->take(10)
    ->get();

if ($unprocessed->isEmpty()) {
    echo "✓ No unprocessed submissions found\n";
} else {
    echo "Found {$unprocessed->count()} unprocessed submissions:\n\n";
    foreach ($unprocessed as $sub) {
        echo "Submission #{$sub->id}\n";
        echo "  Student: {$sub->student_id}\n";
        echo "  Status: {$sub->status}\n";
        echo "  Submitted: {$sub->submitted_at}\n";
        echo "  Has audio: " . ($sub->audio_file_path ? "Yes" : "No") . "\n";
        echo "  Has transcription: " . ($sub->transcription ? "Yes" : "No") . "\n";
        echo "  Has analysis: " . ($sub->tajweed_analysis ? "Yes" : "No") . "\n";
        echo "\n";
    }
}

// 5. Test job dispatch
echo "=== TEST JOB DISPATCH ===\n";
echo "Want to test dispatching a job? This will create a test log entry.\n";
echo "Test log job? (y/N): ";
$handle = fopen("php://stdin", "r");
$line = fgets($handle);
if (trim(strtolower($line)) === 'y') {
    fclose($handle);
    
    \Log::info('=== QUEUE TEST: Dispatching test job at ' . now() . ' ===');
    
    // Create a simple test job
    $testJob = new class {
        use \Illuminate\Bus\Queueable;
        use \Illuminate\Queue\SerializesModels;
        use \Illuminate\Queue\InteractsWithQueue;
        use \Illuminate\Contracts\Queue\ShouldQueue;
        
        public function handle() {
            \Log::info('=== QUEUE TEST: Test job executed successfully at ' . now() . ' ===');
        }
    };
    
    dispatch($testJob);
    echo "✓ Test job dispatched\n";
    echo "  Check logs: tail storage/logs/laravel.log\n";
    
    if ($queueDriver === 'sync') {
        echo "  Since queue is SYNC, check logs now - job should have run\n";
        sleep(1);
        $logFile = storage_path('logs/laravel.log');
        if (file_exists($logFile)) {
            $logs = file_get_contents($logFile);
            if (strpos($logs, 'QUEUE TEST: Test job executed') !== false) {
                echo "  ✓ Test job executed successfully!\n";
            } else {
                echo "  ✗ Test job execution not found in logs\n";
            }
        }
    }
} else {
    fclose($handle);
    echo "Skipped test job\n";
}

echo "\n================================================\n";
echo "  RECOMMENDATIONS\n";
echo "================================================\n";

if ($queueDriver === 'sync') {
    echo "✓ You're using SYNC queue (jobs run immediately)\n";
    echo "  Good for: Development, simple setups\n";
    echo "  No queue workers needed\n\n";
    
    if (!$unprocessed->isEmpty()) {
        echo "⚠ But you have unprocessed submissions!\n";
        echo "  This means jobs are failing during execution\n";
        echo "  Check logs for errors:\n";
        echo "    tail -50 storage/logs/laravel.log\n\n";
        echo "  To manually process a submission:\n";
        echo "    php process_submission_manually.php <submission_id>\n";
    }
    
} elseif ($queueDriver === 'database') {
    echo "You're using DATABASE queue (persistent queue)\n";
    echo "  Good for: Production with queue workers\n\n";
    
    if (!$processes) {
        echo "✗ PROBLEM: No queue workers running!\n";
        echo "  Start workers:\n";
        echo "    php artisan queue:work --daemon\n";
        echo "  Or for supervisor:\n";
        echo "    [program:laravel-worker]\n";
        echo "    command=php artisan queue:work --sleep=3 --tries=3\n";
    }
    
    if ($pendingJobs > 0) {
        echo "\n⚠ You have $pendingJobs pending jobs\n";
        echo "  Process them:\n";
        echo "    php artisan queue:work --once  (process one)\n";
        echo "    php artisan queue:work         (process all)\n";
    }
    
} else {
    echo "You're using '$queueDriver' queue\n";
    echo "  Check documentation for this driver\n";
}

echo "\n================================================\n";
echo "  End of Diagnostics\n";
echo "================================================\n";
