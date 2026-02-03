<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Assignment Submission Status Check ===" . PHP_EOL . PHP_EOL;

$submissions = \App\Models\AssignmentSubmission::with('assignment')
    ->latest()
    ->take(5)
    ->get();

if ($submissions->isEmpty()) {
    echo "No submissions found." . PHP_EOL;
    exit;
}

foreach ($submissions as $submission) {
    echo "Submission ID: {$submission->id}" . PHP_EOL;
    echo "Student ID: {$submission->student_id}" . PHP_EOL;
    echo "Status: {$submission->status}" . PHP_EOL;
    echo "Audio File: " . ($submission->audio_file_path ? "Yes ({$submission->audio_file_path})" : "No") . PHP_EOL;
    echo "Transcription: " . (strlen($submission->transcription ?? '') > 0 ? "Yes (" . strlen($submission->transcription) . " chars)" : "No") . PHP_EOL;
    echo "Tajweed Analysis: " . ($submission->tajweed_analysis ? "Yes" : "No") . PHP_EOL;
    
    if ($submission->assignment) {
        echo "Assignment: {$submission->assignment->surah} {$submission->assignment->start_verse}";
        if ($submission->assignment->end_verse) {
            echo "-{$submission->assignment->end_verse}";
        }
        echo PHP_EOL;
    }
    
    echo "Submitted: {$submission->submitted_at}" . PHP_EOL;
    echo str_repeat("-", 50) . PHP_EOL;
}

echo PHP_EOL . "=== End of Report ===" . PHP_EOL;
