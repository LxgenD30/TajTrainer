<?php

/**
 * Full Submission Test
 * 
 * This tests the complete submission flow including Python analyzer
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Testing Full Submission Flow ===\n\n";

// Check if there's an existing submission we can use for testing
$submission = \App\Models\AssignmentSubmission::with(['assignment', 'student'])
    ->whereNotNull('audio_file_path')
    ->latest()
    ->first();

if (!$submission) {
    echo "❌ No submissions found with audio files to test\n";
    echo "   Please submit an assignment first through the web interface\n";
    exit(1);
}

echo "Found submission #" . $submission->id . "\n";
echo "Student: " . $submission->student->name . "\n";
echo "Assignment: " . $submission->assignment->surah . " (" . 
     $submission->assignment->start_verse . 
     ($submission->assignment->end_verse ? "-" . $submission->assignment->end_verse : "") . ")\n";
echo "Audio file: " . $submission->audio_file_path . "\n";

// Check if audio file exists
$fullPath = storage_path('app/public/' . $submission->audio_file_path);
if (!file_exists($fullPath)) {
    echo "\n❌ Audio file not found at: $fullPath\n";
    exit(1);
}

$fileSize = filesize($fullPath);
echo "File size: " . round($fileSize / 1024, 2) . " KB\n";

echo "\n--- Testing Python Analyzer ---\n";

try {
    // Create a test job instance
    $job = new \App\Jobs\ProcessSubmissionAudio($submission->id);
    
    echo "Dispatching ProcessSubmissionAudio job...\n";
    
    // Process synchronously
    \App\Jobs\ProcessSubmissionAudio::dispatchSync($submission->id);
    
    // Reload submission to get updated data
    $submission->refresh();
    
    echo "\n✅ Processing completed!\n\n";
    
    echo "--- Results ---\n";
    echo "Status: " . $submission->status . "\n";
    
    if ($submission->transcription) {
        echo "Transcription: " . substr($submission->transcription, 0, 100) . "...\n";
    } else {
        echo "Transcription: Not available (AssemblyAI may be processing)\n";
    }
    
    if ($submission->tajweed_analysis) {
        $analysis = is_array($submission->tajweed_analysis) 
            ? $submission->tajweed_analysis 
            : json_decode($submission->tajweed_analysis, true);
        
        echo "\nTajweed Analysis:\n";
        if (isset($analysis['overall_score'])) {
            echo "  Overall Score: " . $analysis['overall_score']['score'] . "%\n";
            echo "  Grade: " . $analysis['overall_score']['grade'] . "\n";
        }
        
        if (isset($analysis['madd_analysis'])) {
            echo "  Madd Analysis:\n";
            echo "    Total: " . $analysis['madd_analysis']['total_elongations'] . "\n";
            echo "    Correct: " . $analysis['madd_analysis']['correct_elongations'] . "\n";
            echo "    Percentage: " . $analysis['madd_analysis']['percentage'] . "%\n";
        }
        
        if (isset($analysis['noon_sakin_analysis'])) {
            echo "  Noon Sakin Analysis:\n";
            echo "    Total: " . $analysis['noon_sakin_analysis']['total_occurrences'] . "\n";
            echo "    Correct: " . $analysis['noon_sakin_analysis']['correct_pronunciation'] . "\n";
            echo "    Percentage: " . $analysis['noon_sakin_analysis']['percentage'] . "%\n";
        }
    } else {
        echo "Tajweed Analysis: Not available\n";
    }
    
    // Check if score was created
    $score = \App\Models\Score::where('assignment_id', $submission->assignment_id)
        ->where('user_id', $submission->student_id)
        ->first();
    
    if ($score) {
        echo "\nScore Created:\n";
        echo "  Points: " . $score->score . "/" . $submission->assignment->total_marks . "\n";
        echo "  Percentage: " . round(($score->score / $submission->assignment->total_marks) * 100, 1) . "%\n";
        if ($score->feedback) {
            echo "  Feedback: " . substr($score->feedback, 0, 100) . "...\n";
        }
    }
    
    echo "\n✅ Full submission flow test PASSED!\n";
    
} catch (\Exception $e) {
    echo "\n❌ Error during processing:\n";
    echo "   " . $e->getMessage() . "\n";
    echo "\nStack trace:\n";
    echo $e->getTraceAsString() . "\n";
    
    echo "\n💡 Check storage/logs/laravel.log for detailed error information\n";
    exit(1);
}

echo "\n=== Test Complete ===\n";
