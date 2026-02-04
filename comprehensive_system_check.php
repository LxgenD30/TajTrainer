<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "═══════════════════════════════════════════════════════════════\n";
echo "          COMPREHENSIVE SYSTEM CHECK FOR TAJTRAINER            \n";
echo "═══════════════════════════════════════════════════════════════\n\n";

$errors = [];
$warnings = [];
$passed = 0;
$total = 0;

function check($label, $result, $error = '') {
    global $errors, $warnings, $passed, $total;
    $total++;
    if ($result) {
        echo "✅ $label\n";
        $passed++;
    } else {
        echo "❌ $label\n";
        $errors[] = "$label: $error";
    }
}

function warn($label, $message) {
    global $warnings;
    echo "⚠️  $label\n";
    $warnings[] = "$label: $message";
}

// ============ ENVIRONMENT CHECKS ============
echo "\n【 ENVIRONMENT CONFIGURATION 】\n";
echo "───────────────────────────────────────────\n";

check('APP_ENV is set', config('app.env') !== null, 'APP_ENV not configured');
check('APP_KEY is set', config('app.key') !== null, 'APP_KEY not configured');
check('Database connection configured', config('database.connections.mysql.database') !== null, 'DB_DATABASE not configured');

// ============ DATABASE CHECKS ============
echo "\n【 DATABASE CONNECTION 】\n";
echo "───────────────────────────────────────────\n";

try {
    \DB::connection()->getPdo();
    check('Database connection', true);
    
    // Check critical tables exist
    $tables = ['users', 'students', 'teachers', 'classrooms', 'assignments', 
               'assignment_submissions', 'materials', 'scores', 'tajweed_error_logs'];
    
    foreach ($tables as $table) {
        $exists = \Schema::hasTable($table);
        check("Table: $table", $exists, "Table $table does not exist");
    }
    
    // Check critical columns
    echo "\n【 DATABASE SCHEMA VERIFICATION 】\n";
    echo "───────────────────────────────────────────\n";
    
    $columnChecks = [
        'assignment_submissions' => ['status', 'audio_file_path', 'tajweed_analysis', 'student_id', 'assignment_id'],
        'tajweed_error_logs' => ['assignment_submission_id', 'practice_session_id', 'error_type', 'severity'],
        'scores' => ['user_id', 'assignment_id', 'score', 'feedback'],
        'users' => ['email', 'password', 'role_id'],
    ];
    
    foreach ($columnChecks as $table => $columns) {
        foreach ($columns as $column) {
            $exists = \Schema::hasColumn($table, $column);
            check("Column: $table.$column", $exists, "Column $column missing in $table");
        }
    }
    
    // Check assignments table for new columns
    echo "\n【 ASSIGNMENTS TABLE SCHEMA 】\n";
    echo "───────────────────────────────────────────\n";
    
    if (\Schema::hasTable('assignments')) {
        $assignmentColumns = ['surah', 'start_verse', 'end_verse', 'expected_recitation', 'reference_audio_url'];
        foreach ($assignmentColumns as $col) {
            $exists = \Schema::hasColumn('assignments', $col);
            check("Column: assignments.$col", $exists, "Column $col missing in assignments table");
        }
        
        // Note: surah_number is a form field only, not stored in database
        // The surah name is stored, and surah_number is derived when needed
        
        // Check reference_audio_url column type
        try {
            $columnType = \DB::select("SHOW COLUMNS FROM assignments WHERE Field = 'reference_audio_url'");
            if (!empty($columnType)) {
                $type = strtolower($columnType[0]->Type);
                $isText = (strpos($type, 'text') !== false);
                check('reference_audio_url is TEXT type', $isText, 
                      "Column is $type - should be TEXT for JSON storage. Run migration: 2026_02_04_193303_change_reference_audio_url_to_text_in_assignments_table.php");
            }
        } catch (\Exception $e) {
            warn('Column type check', $e->getMessage());
        }
    }
    
} catch (\Exception $e) {
    check('Database connection', false, $e->getMessage());
}

// ============ STORAGE CHECKS ============
echo "\n【 STORAGE & FILE SYSTEM 】\n";
echo "───────────────────────────────────────────\n";

$storagePaths = [
    storage_path('app/public/audio'),
    storage_path('app/public/materials'),
    storage_path('app/public/references'),
    storage_path('app/public/submissions'),
    storage_path('app/temp_audio'),
    storage_path('logs'),
    storage_path('framework/cache'),
    storage_path('framework/sessions'),
    storage_path('framework/views'),
];

foreach ($storagePaths as $path) {
    $exists = is_dir($path);
    $writable = $exists && is_writable($path);
    check("Directory: " . basename($path), $exists, "Directory does not exist: $path");
    if ($exists) {
        check("Writable: " . basename($path), $writable, "Directory not writable: $path");
    }
}

// Check symbolic link
$publicStorage = public_path('storage');
check('Public storage symlink', is_link($publicStorage) || is_dir($publicStorage), 
      'Run: php artisan storage:link');

// ============ FFMPEG CHECKS ============
echo "\n【 FFMPEG FOR AUDIO CONCATENATION 】\n";
echo "───────────────────────────────────────────\n";

// Check if ffmpeg is installed
$isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
$ffmpegCmd = $isWindows ? 'where ffmpeg' : 'which ffmpeg';
$ffmpegOutput = [];
exec($ffmpegCmd . ' 2>&1', $ffmpegOutput, $ffmpegReturn);

if ($ffmpegReturn === 0 && !empty($ffmpegOutput)) {
    check('FFmpeg installed', true);
    echo "   Path: " . trim($ffmpegOutput[0]) . "\n";
    
    // Check ffmpeg version
    exec('ffmpeg -version 2>&1', $versionOutput, $versionReturn);
    if ($versionReturn === 0 && isset($versionOutput[0])) {
        echo "   Version: " . explode(' ', $versionOutput[0])[2] . "\n";
    }
} else {
    check('FFmpeg installed', false, 'FFmpeg not found - required for multi-verse audio concatenation');
}

// Check if concatenated audio files exist
$referencesPath = storage_path('app/public/references');
if (is_dir($referencesPath)) {
    $audioFiles = glob($referencesPath . '/*.mp3');
    if (count($audioFiles) > 0) {
        check('Concatenated audio files exist', true);
        echo "   Found: " . count($audioFiles) . " reference audio files\n";
    } else {
        warn('No concatenated audio files', 'Create assignments with multiple verses to generate files');
    }
}

// ============ API KEY CHECKS ============
echo "\n【 EXTERNAL API CONFIGURATION 】\n";
echo "───────────────────────────────────────────\n";

$assemblyaiKey = config('services.assemblyai.api_key');
$openaiKey = config('services.openai.api_key');

check('AssemblyAI API key set', !empty($assemblyaiKey), 'ASSEMBLYAI_API_KEY not configured');
if (!empty($assemblyaiKey)) {
    check('AssemblyAI key format', strlen($assemblyaiKey) === 32, 'Key should be 32 characters');
}

check('OpenAI API key set', !empty($openaiKey), 'OPENAI_API_KEY not configured');
if (!empty($openaiKey)) {
    check('OpenAI key prefix', str_starts_with($openaiKey, 'sk-'), 'Key should start with sk-');
}

// ============ PYTHON ENVIRONMENT ============
echo "\n【 PYTHON INTEGRATION 】\n";
echo "───────────────────────────────────────────\n";

// Detect Python path based on environment
$pythonPaths = [
    env('PYTHON_PATH', '/usr/bin/python3'),  // From .env (Linux production)
    base_path('python/venv/bin/python'),      // Linux venv
    base_path('.venv/Scripts/python.exe'),    // Windows venv
    '/usr/bin/python3',                       // System Python3 (Linux)
    'C:\\Python\\python.exe',                 // System Python (Windows)
];

$pythonPath = null;
foreach ($pythonPaths as $path) {
    if (file_exists($path)) {
        $pythonPath = $path;
        break;
    }
}

if ($pythonPath) {
    check('Python executable found', true);
    echo "   Path: $pythonPath\n";
    
    // Test Python accessibility
    $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
    $pythonCmd = $isWindows ? '"' . $pythonPath . '"' : $pythonPath;
    $pythonCmd .= ' -c "import sys; print(sys.version)"';
    
    exec($pythonCmd . ' 2>&1', $output, $returnCode);
    check('Python executable works', $returnCode === 0, 'Python execution failed');
    
    if ($returnCode === 0 && isset($output[0])) {
        echo "   Python version: " . trim($output[0]) . "\n";
    }
    
    // Check Python dependencies
    $dependencies = ['librosa', 'soundfile', 'parselmouth', 'openai', 'fastdtw'];
    $pythonCheckCmd = $isWindows ? '"' . $pythonPath . '"' : $pythonPath;
    $pythonCheckCmd .= ' -c "';
    $pythonCheckCmd .= 'import sys; modules=[';
    foreach ($dependencies as $dep) {
        $pythonCheckCmd .= "\"$dep\",";
    }
    $pythonCheckCmd .= ']; ';
    $pythonCheckCmd .= 'missing=[m for m in modules if __import__(\"importlib.util\").util.find_spec(m) is None]; ';
    $pythonCheckCmd .= 'print(\"MISSING:\" + \",\".join(missing) if missing else \"OK\")"';
    
    $depOutput = [];
    exec($pythonCheckCmd . ' 2>&1', $depOutput, $depReturn);
    if ($depReturn === 0 && isset($depOutput[0])) {
        if (trim($depOutput[0]) === 'OK') {
            check('Python dependencies installed', true);
        } else {
            $missing = str_replace('MISSING:', '', trim($depOutput[0]));
            check('Python dependencies installed', false, "Missing: $missing");
        }
    }
    
    // Check tajweed_analyzer.py exists
    $analyzerPath = base_path('python/tajweed_analyzer.py');
    check('Tajweed analyzer script', file_exists($analyzerPath), 
          'tajweed_analyzer.py not found');
} else {
    check('Python executable found', false, 'No Python found in common locations');
}

// ============ ROUTES CHECKS ============
echo "\n【 CRITICAL ROUTES 】\n";
echo "───────────────────────────────────────────\n";

$criticalRoutes = [
    'home',
    'classroom.index',
    'classroom.show',
    'classroom.create',
    'assignment.show',
    'assignment.create',
    'teacher.student.submissions',
    'teacher.submission.grade',
    'teacher.submission.update.grade',
    'student.classes',
    'student.assignment.submit',
    'student.assignment.store',
    'student.practice',
    'student.practice.submit',
    'materials.index',
    'materials.show',
];

foreach ($criticalRoutes as $routeName) {
    try {
        $url = route($routeName, ['id' => 1, 'classroom' => 1, 'student' => 1, 'submission' => 1, 'assignment' => 1, 'material' => 1], false);
        check("Route: $routeName", true);
    } catch (\Exception $e) {
        check("Route: $routeName", false, $e->getMessage());
    }
}

// ============ MODEL RELATIONSHIPS ============
echo "\n【 MODEL RELATIONSHIPS 】\n";
echo "───────────────────────────────────────────\n";

try {
    // Test if models can be instantiated
    $modelTests = [
        'User' => \App\Models\User::class,
        'Student' => \App\Models\Student::class,
        'Teacher' => \App\Models\Teacher::class,
        'Classroom' => \App\Models\Classroom::class,
        'Assignment' => \App\Models\Assignment::class,
        'AssignmentSubmission' => \App\Models\AssignmentSubmission::class,
        'Material' => \App\Models\Material::class,
        'Score' => \App\Models\Score::class,
        'TajweedErrorLog' => \App\Models\TajweedErrorLog::class,
    ];
    
    foreach ($modelTests as $name => $class) {
        try {
            $model = new $class();
            check("Model: $name", true);
        } catch (\Exception $e) {
            check("Model: $name", false, $e->getMessage());
        }
    }
} catch (\Exception $e) {
    warn('Model checks', $e->getMessage());
}

// ============ JOB CLASSES ============
echo "\n【 QUEUE JOBS 】\n";
echo "───────────────────────────────────────────\n";

$jobPath = app_path('Jobs/ProcessSubmissionAudio.php');
check('ProcessSubmissionAudio job', file_exists($jobPath), 
      'ProcessSubmissionAudio.php not found');

// ============ SERVICE CLASSES ============
echo "\n【 SERVICE CLASSES 】\n";
echo "───────────────────────────────────────────\n";

$servicePaths = [
    'ProgressTracker' => app_path('Services/ProgressTracker.php'),
];

foreach ($servicePaths as $name => $path) {
    check("Service: $name", file_exists($path), "$name service not found");
}

// ============ CONFIGURATION FILES ============
echo "\n【 CONFIGURATION FILES 】\n";
echo "───────────────────────────────────────────\n";

$configFiles = [
    'app', 'database', 'filesystems', 'queue', 'services', 'telegram'
];

foreach ($configFiles as $config) {
    $path = config_path("$config.php");
    check("Config: $config", file_exists($path), "Config file $config.php not found");
}

// ============ DATA INTEGRITY CHECKS ============
echo "\n【 DATA INTEGRITY 】\n";
echo "───────────────────────────────────────────\n";

try {
    // Check for orphaned records
    $orphanedSubmissions = \DB::table('assignment_submissions')
        ->leftJoin('assignments', 'assignment_submissions.assignment_id', '=', 'assignments.assignment_id')
        ->whereNull('assignments.assignment_id')
        ->count();
    
    if ($orphanedSubmissions > 0) {
        warn('Orphaned submissions', "$orphanedSubmissions submissions without assignments");
    } else {
        check('No orphaned submissions', true);
    }
    
    // Check for submissions with invalid status
    $invalidStatus = \DB::table('assignment_submissions')
        ->whereNotIn('status', ['pending', 'submitted', 'graded'])
        ->count();
    
    if ($invalidStatus > 0) {
        warn('Invalid submission status', "$invalidStatus submissions with invalid status");
    } else {
        check('Valid submission statuses', true);
    }
    
    // Check for assignments with multi-verse but no concatenated audio
    $multiVerseAssignments = \DB::table('assignments')
        ->whereRaw('start_verse != end_verse OR end_verse IS NOT NULL')
        ->count();
    
    if ($multiVerseAssignments > 0) {
        echo "   Found $multiVerseAssignments multi-verse assignments\n";
        
        $missingConcatenated = \DB::table('assignments')
            ->whereRaw('start_verse != end_verse OR end_verse IS NOT NULL')
            ->where(function($query) {
                $query->whereNull('reference_audio_url')
                      ->orWhere('reference_audio_url', '=', '')
                      ->orWhere('reference_audio_url', 'NOT LIKE', 'references/%');
            })
            ->count();
        
        if ($missingConcatenated > 0) {
            warn('Multi-verse assignments without concatenated audio', 
                 "$missingConcatenated assignments need audio regeneration");
        } else {
            check('All multi-verse assignments have concatenated audio', true);
        }
    }
    
} catch (\Exception $e) {
    warn('Data integrity checks', $e->getMessage());
}

// ============ FEATURE VERIFICATION ============
echo "\n【 RECENT FEATURES VERIFICATION 】\n";
echo "───────────────────────────────────────────\n";

// Check if audio protection is implemented in view
$assignmentShowPath = resource_path('views/assignment/show.blade.php');
if (file_exists($assignmentShowPath)) {
    $content = file_get_contents($assignmentShowPath);
    $hasDownloadProtection = strpos($content, 'controlsList="nodownload') !== false;
    $hasRoleCheck = strpos($content, 'role_id') !== false && strpos($content, 'nodownload') !== false;
    
    check('Audio download protection implemented', $hasDownloadProtection && $hasRoleCheck,
          'Student audio download protection not found in assignment show view');
} else {
    warn('Assignment show view', 'File not found');
}

// Check if grade submission has welcome banner
$gradeSubmissionPath = resource_path('views/teachers/grade-submission.blade.php');
if (file_exists($gradeSubmissionPath)) {
    $content = file_get_contents($gradeSubmissionPath);
    $hasWelcomeBanner = strpos($content, 'welcome-banner') !== false;
    $hasStudentName = strpos($content, '$submission->student->name') !== false;
    
    check('Grade submission welcome banner', $hasWelcomeBanner && $hasStudentName,
          'Welcome banner with student name not found in grade submission page');
} else {
    warn('Grade submission view', 'File not found');
}

// Check if ProcessSubmissionAudio job handles concatenated audio
$jobPath = app_path('Jobs/ProcessSubmissionAudio.php');
if (file_exists($jobPath)) {
    $content = file_get_contents($jobPath);
    $handlesConcatenated = strpos($content, 'references/') !== false;
    $hasReferenceAudioPath = strpos($content, 'referenceAudioPath') !== false;
    
    check('Job handles concatenated audio files', $handlesConcatenated && $hasReferenceAudioPath,
          'ProcessSubmissionAudio job not updated for concatenated audio');
} else {
    warn('ProcessSubmissionAudio job', 'File not found');
}

// Check AssignmentController has concatenation method
$controllerPath = app_path('Http/Controllers/AssignmentController.php');
if (file_exists($controllerPath)) {
    $content = file_get_contents($controllerPath);
    $hasConcatenateMethod = strpos($content, 'concatenateAudioFiles') !== false;
    $usesFfmpeg = strpos($content, 'ffmpeg') !== false;
    
    check('Audio concatenation implemented', $hasConcatenateMethod && $usesFfmpeg,
          'Audio concatenation method not found in AssignmentController');
} else {
    warn('AssignmentController', 'File not found');
}

// ============ FINAL SUMMARY ============
echo "\n═══════════════════════════════════════════════════════════════\n";
echo "                        SYSTEM CHECK SUMMARY                    \n";
echo "═══════════════════════════════════════════════════════════════\n\n";

echo "✅ Passed: $passed / $total checks\n";

if (count($errors) > 0) {
    echo "\n❌ ERRORS (" . count($errors) . "):\n";
    foreach ($errors as $error) {
        echo "   • $error\n";
    }
}

if (count($warnings) > 0) {
    echo "\n⚠️  WARNINGS (" . count($warnings) . "):\n";
    foreach ($warnings as $warning) {
        echo "   • $warning\n";
    }
}

$score = $total > 0 ? round(($passed / $total) * 100, 1) : 0;
echo "\n───────────────────────────────────────────\n";
echo "Overall System Health: $score%\n";

if ($score >= 95 && count($errors) === 0) {
    echo "\n✅ SYSTEM READY FOR PRODUCTION DEPLOYMENT\n";
} elseif ($score >= 80) {
    echo "\n⚠️  SYSTEM FUNCTIONAL BUT HAS WARNINGS\n";
    echo "Review warnings before deployment\n";
} else {
    echo "\n❌ SYSTEM NOT READY FOR DEPLOYMENT\n";
    echo "Fix critical errors before proceeding\n";
}

echo "═══════════════════════════════════════════════════════════════\n\n";
