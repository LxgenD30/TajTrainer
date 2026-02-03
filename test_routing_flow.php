<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "═══════════════════════════════════════════════════════════════\n";
echo "        ROUTING & DATA FLOW SIMULATION TEST                    \n";
echo "═══════════════════════════════════════════════════════════════\n\n";

// Test the specific route that was failing
echo "【 TESTING TEACHER SUBMISSION PAGE 】\n";
echo "───────────────────────────────────────────\n";

try {
    // Get a classroom and student for testing
    $classroom = \App\Models\Classroom::first();
    $student = \App\Models\Student::first();
    
    if ($classroom && $student) {
        echo "✅ Test Data Found:\n";
        echo "   Classroom ID: {$classroom->id}\n";
        echo "   Student ID: {$student->id}\n\n";
        
        // Test the route URL generation
        try {
            $url = route('teacher.student.submissions', [
                'classroom' => $classroom->id, 
                'student' => $student->id
            ]);
            echo "✅ Route URL Generated: $url\n\n";
        } catch (\Exception $e) {
            echo "❌ Route Generation Failed: {$e->getMessage()}\n\n";
        }
        
        // Test data flow
        echo "【 DATA FLOW SIMULATION 】\n";
        echo "───────────────────────────────────────────\n";
        
        // Get assignments for classroom
        $assignments = \App\Models\Assignment::where('class_id', $classroom->id)
            ->with('material')
            ->get();
        echo "✅ Assignments Query: " . $assignments->count() . " assignments found\n";
        
        // Get submissions from student
        $submissions = \App\Models\AssignmentSubmission::where('student_id', $student->id)
            ->whereIn('assignment_id', $assignments->pluck('assignment_id'))
            ->with(['assignment'])
            ->get();
        echo "✅ Submissions Query: " . $submissions->count() . " submissions found\n\n";
        
        // Test grading route for each submission
        echo "【 TESTING GRADING ROUTES 】\n";
        echo "───────────────────────────────────────────\n";
        
        foreach ($submissions as $submission) {
            try {
                // Test teacher.submission.grade route
                $gradeUrl = route('teacher.submission.grade', $submission->id);
                echo "✅ Grade View Route: submission #{$submission->id} -> $gradeUrl\n";
                
                // Test teacher.submission.update.grade route
                $updateUrl = route('teacher.submission.update.grade', $submission->id);
                echo "✅ Grade Update Route: submission #{$submission->id} -> $updateUrl\n";
                
                // Check submission data integrity
                if ($submission->assignment_id && $submission->student_id) {
                    echo "   ✓ Data integrity: Valid assignment_id and student_id\n";
                }
                
                if ($submission->status) {
                    echo "   ✓ Status: {$submission->status}\n";
                }
                
                // Check for tajweed errors
                $errors = \App\Models\TajweedErrorLog::where('assignment_submission_id', $submission->id)->count();
                echo "   ✓ Tajweed Errors: $errors errors logged\n";
                
                echo "\n";
            } catch (\Exception $e) {
                echo "❌ Route Error for submission #{$submission->id}: {$e->getMessage()}\n\n";
            }
        }
        
        // Test complete submission flow
        echo "【 COMPLETE SUBMISSION FLOW TEST 】\n";
        echo "───────────────────────────────────────────\n";
        
        if ($submissions->count() > 0) {
            $testSubmission = $submissions->first();
            echo "Testing submission ID: {$testSubmission->id}\n\n";
            
            // Step 1: Student submits
            echo "1️⃣ Student Submission:\n";
            echo "   Assignment: {$testSubmission->assignment->title}\n";
            echo "   Student: {$testSubmission->student->name}\n";
            echo "   Status: {$testSubmission->status}\n";
            echo "   Audio File: " . ($testSubmission->audio_file_path ? '✓ Present' : '✗ Missing') . "\n\n";
            
            // Step 2: Python analyzer processes
            echo "2️⃣ Python Analyzer:\n";
            $pythonPath = base_path('.venv/Scripts/python.exe');
            $analyzerPath = base_path('python/tajweed_analyzer.py');
            if (file_exists($pythonPath) && file_exists($analyzerPath)) {
                echo "   ✓ Python executable: Available\n";
                echo "   ✓ Analyzer script: Available\n";
                echo "   ✓ Tajweed Analysis: " . ($testSubmission->tajweed_analysis ? 'Processed' : 'Pending') . "\n\n";
            } else {
                echo "   ✗ Python setup incomplete\n\n";
            }
            
            // Step 3: Teacher grades
            echo "3️⃣ Teacher Grading:\n";
            $score = \App\Models\Score::where('user_id', $testSubmission->student_id)
                ->where('assignment_id', $testSubmission->assignment_id)
                ->first();
            
            if ($score) {
                echo "   ✓ Score: {$score->score}/{$testSubmission->assignment->total_marks}\n";
                echo "   ✓ Feedback: " . (strlen($score->feedback) > 50 ? substr($score->feedback, 0, 50) . '...' : $score->feedback) . "\n";
            } else {
                echo "   ⏳ Not yet graded\n";
            }
        } else {
            echo "⚠️  No submissions found for testing\n";
        }
        
    } else {
        echo "⚠️  No test data available (classroom or student missing)\n";
        echo "   Classrooms: " . \App\Models\Classroom::count() . "\n";
        echo "   Students: " . \App\Models\Student::count() . "\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Test Failed: {$e->getMessage()}\n";
    echo "Stack Trace:\n{$e->getTraceAsString()}\n";
}

// Test all critical routes with proper parameters
echo "\n【 ROUTE ACCESSIBILITY TEST 】\n";
echo "───────────────────────────────────────────\n";

$routeTests = [
    ['name' => 'home', 'params' => []],
    ['name' => 'classroom.index', 'params' => []],
    ['name' => 'student.classes', 'params' => []],
    ['name' => 'student.practice', 'params' => []],
    ['name' => 'materials.index', 'params' => []],
];

foreach ($routeTests as $test) {
    try {
        $url = route($test['name'], $test['params']);
        echo "✅ {$test['name']} -> $url\n";
    } catch (\Exception $e) {
        echo "❌ {$test['name']} -> ERROR: {$e->getMessage()}\n";
    }
}

echo "\n═══════════════════════════════════════════════════════════════\n";
echo "                     SIMULATION COMPLETE                        \n";
echo "═══════════════════════════════════════════════════════════════\n\n";
