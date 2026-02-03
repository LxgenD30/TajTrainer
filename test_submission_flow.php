#!/usr/bin/env php
<?php
/**
 * Test Script for Hosted Server - Assignment Submission Flow
 * Tests the complete flow from audio submission to grading
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "================================================\n";
echo "  Assignment Submission Flow Test\n";
echo "  Date: " . date('Y-m-d H:i:s') . "\n";
echo "================================================\n\n";

// Test 1: Check Latest Submission
echo "=== TEST 1: Latest Assignment Submission ===\n";
$submission = \App\Models\AssignmentSubmission::with(['assignment', 'student'])
    ->latest()
    ->first();

if (!$submission) {
    echo "✗ No submissions found in database\n";
    echo "  Action: Create a test submission first\n\n";
} else {
    echo "✓ Found submission #{$submission->id}\n";
    echo "  Student ID: {$submission->student_id}\n";
    echo "  Assignment: {$submission->assignment->surah} {$submission->assignment->start_verse}";
    if ($submission->assignment->end_verse) {
        echo "-{$submission->assignment->end_verse}";
    }
    echo "\n";
    echo "  Status: {$submission->status}\n";
    echo "  Submitted: {$submission->submitted_at}\n\n";
    
    // Check audio file
    echo "  Audio File Check:\n";
    if ($submission->audio_file_path) {
        echo "    Path: {$submission->audio_file_path}\n";
        $fullPath = storage_path('app/public/' . $submission->audio_file_path);
        if (file_exists($fullPath)) {
            $size = filesize($fullPath);
            echo "    ✓ File exists (" . round($size / 1024, 2) . " KB)\n";
        } else {
            echo "    ✗ File NOT found at: $fullPath\n";
        }
    } else {
        echo "    ✗ No audio file path set\n";
    }
    echo "\n";
    
    // Check transcription
    echo "  Transcription Check:\n";
    if (!empty($submission->transcription)) {
        echo "    ✓ Has transcription (" . strlen($submission->transcription) . " chars)\n";
        echo "    Preview: " . mb_substr($submission->transcription, 0, 80) . "...\n";
    } else {
        echo "    ✗ No transcription\n";
    }
    echo "\n";
    
    // Check Tajweed analysis
    echo "  Tajweed Analysis Check:\n";
    if ($submission->tajweed_analysis) {
        $analysis = is_string($submission->tajweed_analysis) 
            ? json_decode($submission->tajweed_analysis, true) 
            : $submission->tajweed_analysis;
        
        if ($analysis) {
            echo "    ✓ Has Tajweed analysis (JSON)\n";
            
            if (isset($analysis['overall_score'])) {
                echo "    Overall Score: {$analysis['overall_score']['score']}%\n";
            }
            
            if (isset($analysis['whisper_transcription'])) {
                echo "    ✓ Contains Whisper transcription\n";
            } else {
                echo "    ⚠ Missing whisper_transcription field\n";
            }
            
            if (isset($analysis['madd_analysis'])) {
                echo "    ✓ Has Madd analysis\n";
            }
            
            if (isset($analysis['noon_sakin_analysis'])) {
                echo "    ✓ Has Noon Sakin analysis\n";
            }
        } else {
            echo "    ✗ Tajweed analysis is not valid JSON\n";
        }
    } else {
        echo "    ✗ No Tajweed analysis\n";
    }
    echo "\n";
    
    // Check score
    echo "  Score Check:\n";
    $score = \App\Models\Score::where('user_id', $submission->student_id)
        ->where('assignment_id', $submission->assignment_id)
        ->first();
    
    if ($score) {
        echo "    ✓ Score created: {$score->score}/{$submission->assignment->total_marks}\n";
        if ($score->feedback) {
            echo "    Feedback: " . mb_substr($score->feedback, 0, 100) . "...\n";
        }
    } else {
        echo "    ✗ No score record found\n";
    }
    echo "\n";
}

// Test 2: Check ProcessSubmissionAudio Code
echo "=== TEST 2: ProcessSubmissionAudio Code Check ===\n";
$jobFile = file_get_contents('app/Jobs/ProcessSubmissionAudio.php');

// Check for new code markers
$markers = [
    'Starting Python analysis (Whisper + Tajweed)' => false,
    'whisper_transcription' => false,
    'getPythonCommand' => false,
    'proc_open' => false,
];

foreach ($markers as $marker => $found) {
    if (strpos($jobFile, $marker) !== false) {
        echo "✓ Found: $marker\n";
    } else {
        echo "✗ Missing: $marker\n";
    }
}

// Check handle() method
preg_match('/public function handle\(\).*?\{(.*?)(?=\n    (?:public|private)|\Z)/s', $jobFile, $matches);
if (isset($matches[1])) {
    $handleMethod = $matches[1];
    
    if (strpos($handleMethod, 'transcribeWithAssemblyAI') !== false) {
        echo "\n✗ CRITICAL: handle() method still calls transcribeWithAssemblyAI!\n";
        echo "  This means OLD CODE is still active\n";
        echo "  Action: git reset --hard origin/main\n";
    } else {
        echo "\n✓ handle() method does NOT call AssemblyAI\n";
    }
    
    if (strpos($handleMethod, 'Starting Python analysis') !== false) {
        echo "✓ handle() method uses NEW CODE\n";
    } else {
        echo "✗ handle() method might be using old approach\n";
    }
}
echo "\n";

// Test 3: Check Recent Logs
echo "=== TEST 3: Recent Laravel Logs ===\n";
$logFile = storage_path('logs/laravel.log');
if (file_exists($logFile)) {
    $logContent = file_get_contents($logFile);
    $lines = explode("\n", $logContent);
    $recentLines = array_slice($lines, -100); // Last 100 lines
    
    $relevantLogs = [];
    foreach ($recentLines as $line) {
        if (strpos($line, 'Processing Audio Job') !== false ||
            strpos($line, 'Python analysis') !== false ||
            strpos($line, 'Whisper transcription') !== false ||
            strpos($line, 'AssemblyAI') !== false) {
            $relevantLogs[] = $line;
        }
    }
    
    if (!empty($relevantLogs)) {
        echo "Found " . count($relevantLogs) . " relevant log entries:\n";
        foreach (array_slice($relevantLogs, -10) as $log) {
            echo "  " . trim($log) . "\n";
        }
    } else {
        echo "⚠ No recent processing logs found\n";
        echo "  This might mean no submissions have been processed recently\n";
    }
    echo "\n";
    
    // Check for errors
    $errorCount = 0;
    foreach ($recentLines as $line) {
        if (strpos($line, 'local.ERROR') !== false) {
            $errorCount++;
        }
    }
    
    if ($errorCount > 0) {
        echo "⚠ Found $errorCount errors in recent logs\n";
        echo "  Check logs for details: tail -50 storage/logs/laravel.log\n";
    } else {
        echo "✓ No recent errors\n";
    }
} else {
    echo "✗ Log file not found: $logFile\n";
}
echo "\n";

// Test 4: Check Python Availability
echo "=== TEST 4: Python Environment ===\n";
$pythonTests = [
    'python3 --version' => 'Python version',
    'which python3' => 'Python location',
    'python3 python/tajweed_analyzer.py --help' => 'Analyzer help'
];

foreach ($pythonTests as $cmd => $desc) {
    echo "$desc:\n";
    $output = shell_exec($cmd . ' 2>&1');
    if ($output) {
        echo "  " . trim(substr($output, 0, 100)) . "\n";
    } else {
        echo "  ✗ No output\n";
    }
}
echo "\n";

// Test 5: Test Python Analyzer Directly
echo "=== TEST 5: Direct Python Test ===\n";
if ($submission && $submission->audio_file_path) {
    $audioPath = storage_path('app/public/' . $submission->audio_file_path);
    $expectedText = "بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ"; // Sample text
    
    if (file_exists($audioPath)) {
        echo "Testing with submission #{$submission->id} audio file...\n";
        echo "Audio: $audioPath\n";
        
        $cmd = sprintf(
            'python3 python/tajweed_analyzer.py %s %s',
            escapeshellarg($audioPath),
            escapeshellarg($expectedText)
        );
        
        echo "Command: $cmd\n\n";
        echo "Running test (this may take 30-60 seconds)...\n";
        
        $startTime = microtime(true);
        $output = shell_exec($cmd . ' 2>&1');
        $duration = round(microtime(true) - $startTime, 2);
        
        echo "Duration: {$duration}s\n";
        
        if ($output) {
            // Try to parse as JSON
            $result = json_decode($output, true);
            if ($result) {
                echo "✓ Python returned valid JSON\n";
                
                if (isset($result['whisper_transcription'])) {
                    echo "✓ Has whisper_transcription\n";
                    echo "  Text: " . mb_substr($result['whisper_transcription'], 0, 80) . "...\n";
                } else {
                    echo "✗ Missing whisper_transcription field\n";
                }
                
                if (isset($result['overall_score'])) {
                    echo "✓ Has overall_score: {$result['overall_score']['score']}%\n";
                } else {
                    echo "✗ Missing overall_score\n";
                }
                
                if (isset($result['madd_analysis'])) {
                    echo "✓ Has madd_analysis\n";
                }
                
                if (isset($result['noon_sakin_analysis'])) {
                    echo "✓ Has noon_sakin_analysis\n";
                }
            } else {
                echo "✗ Python output is not valid JSON\n";
                echo "Output preview:\n";
                echo substr($output, 0, 500) . "\n...\n";
            }
        } else {
            echo "✗ No output from Python\n";
        }
    } else {
        echo "✗ Audio file not found: $audioPath\n";
        echo "  Cannot test Python analyzer without audio file\n";
    }
} else {
    echo "⚠ No recent submission with audio file to test\n";
}
echo "\n";

// Final Summary
echo "================================================\n";
echo "  SUMMARY\n";
echo "================================================\n";

$issues = [];

if (!$submission) {
    $issues[] = "No submissions in database";
} elseif (!$submission->transcription) {
    $issues[] = "Latest submission has no transcription";
} elseif (!$submission->tajweed_analysis) {
    $issues[] = "Latest submission has no Tajweed analysis";
}

if (strpos($jobFile, 'Starting Python analysis (Whisper + Tajweed)') === false) {
    $issues[] = "ProcessSubmissionAudio missing new code markers";
}

preg_match('/public function handle\(\).*?\{(.*?)(?=\n    (?:public|private)|\Z)/s', $jobFile, $handleMatches);
if (isset($handleMatches[1]) && strpos($handleMatches[1], 'transcribeWithAssemblyAI') !== false) {
    $issues[] = "CRITICAL: Old code still active (AssemblyAI being called)";
}

if (!empty($issues)) {
    echo "✗ ISSUES DETECTED:\n";
    foreach ($issues as $issue) {
        echo "  - $issue\n";
    }
    echo "\n";
    echo "RECOMMENDED ACTIONS:\n";
    echo "1. Pull latest code: git pull origin main\n";
    echo "2. Clear caches: php artisan cache:clear && php artisan view:clear\n";
    echo "3. Restart PHP-FPM: sudo systemctl restart php8.2-fpm\n";
    echo "4. Test new submission\n";
} else {
    echo "✓ No critical issues detected\n";
    echo "  System appears to be working correctly\n";
}

echo "\n================================================\n";
echo "  End of Tests\n";
echo "================================================\n";
