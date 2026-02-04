<?php
/**
 * TajTrainer System Health Check
 * Run this file to verify all system components are functioning correctly
 * 
 * Usage: php system_health_check.php
 */

require __DIR__.'/vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Colors for terminal output
class Colors {
    public static $GREEN = "\033[32m";
    public static $RED = "\033[31m";
    public static $YELLOW = "\033[33m";
    public static $BLUE = "\033[34m";
    public static $RESET = "\033[0m";
    public static $BOLD = "\033[1m";
}

function printHeader($text) {
    echo "\n" . Colors::$BOLD . Colors::$BLUE . "========================================\n";
    echo "  " . strtoupper($text) . "\n";
    echo "========================================" . Colors::$RESET . "\n\n";
}

function printSuccess($text) {
    echo Colors::$GREEN . "✓ " . $text . Colors::$RESET . "\n";
}

function printError($text) {
    echo Colors::$RED . "✗ " . $text . Colors::$RESET . "\n";
}

function printWarning($text) {
    echo Colors::$YELLOW . "⚠ " . $text . Colors::$RESET . "\n";
}

function printInfo($text) {
    echo Colors::$BLUE . "ℹ " . $text . Colors::$RESET . "\n";
}

$startTime = microtime(true);
$totalTests = 0;
$passedTests = 0;
$failedTests = 0;

echo Colors::$BOLD . "\n╔═══════════════════════════════════════════════════════╗\n";
echo "║        TAJTRAINER SYSTEM HEALTH CHECK REPORT         ║\n";
echo "║                  " . date('Y-m-d H:i:s') . "                  ║\n";
echo "╚═══════════════════════════════════════════════════════╝" . Colors::$RESET . "\n";

// ============================================================
// 1. DATABASE CONNECTION & STRUCTURE
// ============================================================
printHeader("1. Database Connection & Structure");

try {
    DB::connection()->getPdo();
    $totalTests++;
    $passedTests++;
    printSuccess("Database connection successful");
    
    // Check critical tables
    $requiredTables = [
        'users', 'role', 'students', 'teachers', 'classrooms', 
        'assignments', 'materials', 'material_items', 'assignment_submissions', 
        'scores', 'practice_sessions', 'tajweed_error_logs'
    ];
    
    foreach ($requiredTables as $table) {
        $totalTests++;
        if (DB::getSchemaBuilder()->hasTable($table)) {
            $count = DB::table($table)->count();
            $passedTests++;
            printSuccess("Table '{$table}' exists ({$count} records)");
        } else {
            $failedTests++;
            printError("Table '{$table}' is missing!");
        }
    }
} catch (\Exception $e) {
    $totalTests++;
    $failedTests++;
    printError("Database connection failed: " . $e->getMessage());
}

// ============================================================
// 2. API KEYS & CONFIGURATION
// ============================================================
printHeader("2. API Keys & Configuration");

// OpenAI API
$totalTests++;
$openaiKey = env('OPENAI_API_KEY');
if ($openaiKey && $openaiKey !== '') {
    $passedTests++;
    printSuccess("OpenAI API key configured");
    
    // Test OpenAI connection (skip if local environment due to SSL issues)
    if (env('APP_ENV') !== 'local') {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $openaiKey,
            ])->timeout(10)->get('https://api.openai.com/v1/models');
            
            if ($response->successful()) {
                printSuccess("OpenAI API connection verified");
            } else {
                printWarning("OpenAI API key may be invalid (Status: " . $response->status() . ")");
            }
        } catch (\Exception $e) {
            printWarning("OpenAI API test skipped: " . substr($e->getMessage(), 0, 50) . "...");
        }
    } else {
        printInfo("OpenAI API connection test skipped (local environment)");
    }
} else {
    $failedTests++;
    printError("OpenAI API key not configured");
}

// Tavily API
$totalTests++;
$tavilyKey = env('TAVILY_API_KEY');
if ($tavilyKey && $tavilyKey !== '') {
    $passedTests++;
    printSuccess("Tavily API key configured");
    
    // Test Tavily connection (skip if local environment due to SSL issues)
    if (env('APP_ENV') !== 'local') {
        try {
            $response = Http::timeout(10)->post('https://api.tavily.com/search', [
                'api_key' => $tavilyKey,
                'query' => 'test',
                'max_results' => 1
            ]);
            
            if ($response->successful()) {
                printSuccess("Tavily API connection verified");
            } else {
                printWarning("Tavily API key may be invalid (Status: " . $response->status() . ")");
            }
        } catch (\Exception $e) {
            printWarning("Tavily API test skipped: " . substr($e->getMessage(), 0, 50) . "...");
        }
    } else {
        printInfo("Tavily API connection test skipped (local environment)");
    }
} else {
    $failedTests++;
    printError("Tavily API key not configured");
}

// Telegram Bot
$totalTests++;
$telegramToken = env('TELEGRAM_BOT_TOKEN');
if ($telegramToken && $telegramToken !== '') {
    $passedTests++;
    printSuccess("Telegram Bot token configured");
    
    // Test Telegram Bot (skip if local environment due to SSL issues)
    if (env('APP_ENV') !== 'local') {
        try {
            $response = Http::timeout(10)->get('https://api.telegram.org/bot' . $telegramToken . '/getMe');
            
            if ($response->successful()) {
                $botInfo = $response->json();
                printSuccess("Telegram Bot verified: @" . ($botInfo['result']['username'] ?? 'unknown'));
            } else {
                printWarning("Telegram Bot token may be invalid");
            }
        } catch (\Exception $e) {
            printWarning("Telegram Bot test skipped: " . substr($e->getMessage(), 0, 50) . "...");
        }
    } else {
        printInfo("Telegram Bot test skipped (local environment)");
    }
} else {
    $passedTests++;
    printWarning("Telegram Bot token not configured (optional)");
}

// ============================================================
// 3. STORAGE & FILE SYSTEM
// ============================================================
printHeader("3. Storage & File System");

$storagePaths = [
    'public' => 'storage/app/public',
    'materials' => 'storage/app/public/materials',
    'submissions' => 'storage/app/public/submissions',
    'profile_pictures' => 'storage/app/public/profile_pictures',
];

foreach ($storagePaths as $name => $path) {
    $totalTests++;
    $fullPath = base_path($path);
    
    if (is_dir($fullPath) && is_writable($fullPath)) {
        $passedTests++;
        printSuccess("Storage path '{$name}' exists and is writable");
    } else {
        $failedTests++;
        printError("Storage path '{$name}' is missing or not writable: {$path}");
    }
}

// Check symbolic link
$totalTests++;
if (file_exists(public_path('storage'))) {
    $passedTests++;
    printSuccess("Public storage symlink exists");
} else {
    $failedTests++;
    printError("Public storage symlink missing. Run: php artisan storage:link");
}

// ============================================================
// 4. CRUD OPERATIONS TEST
// ============================================================
printHeader("4. CRUD Operations Test");

// Test User Read
$totalTests++;
try {
    $userCount = DB::table('users')->count();
    $passedTests++;
    printSuccess("Users table readable ({$userCount} users)");
} catch (\Exception $e) {
    $failedTests++;
    printError("Users table read failed: " . $e->getMessage());
}

// Test Classrooms
$totalTests++;
try {
    $classroomCount = DB::table('classrooms')->count();
    $passedTests++;
    printSuccess("Classrooms table readable ({$classroomCount} classrooms)");
} catch (\Exception $e) {
    $failedTests++;
    printError("Classrooms table read failed: " . $e->getMessage());
}

// Test Assignments
$totalTests++;
try {
    $assignmentCount = DB::table('assignments')->count();
    $passedTests++;
    printSuccess("Assignments table readable ({$assignmentCount} assignments)");
} catch (\Exception $e) {
    $failedTests++;
    printError("Assignments table read failed: " . $e->getMessage());
}

// Test Materials & Material Items
$totalTests++;
try {
    $materialCount = DB::table('materials')->count();
    $itemCount = DB::table('material_items')->count();
    $passedTests++;
    printSuccess("Materials system readable ({$materialCount} materials, {$itemCount} items)");
} catch (\Exception $e) {
    $failedTests++;
    printError("Materials table read failed: " . $e->getMessage());
}

// Test Submissions
$totalTests++;
try {
    $submissionCount = DB::table('assignment_submissions')->count();
    $passedTests++;
    printSuccess("Assignment submissions table readable ({$submissionCount} submissions)");
} catch (\Exception $e) {
    $failedTests++;
    printError("Assignment submissions table read failed: " . $e->getMessage());
}

// Test Scores
$totalTests++;
try {
    $scoreCount = DB::table('scores')->count();
    $passedTests++;
    printSuccess("Scores table readable ({$scoreCount} scores)");
} catch (\Exception $e) {
    $failedTests++;
    printError("Scores table read failed: " . $e->getMessage());
}

// ============================================================
// 5. PYTHON INTEGRATION
// ============================================================
printHeader("5. Python Integration");

// Check Python executable
$totalTests++;
$pythonPath = env('PYTHON_PATH', 'python');
exec("$pythonPath --version 2>&1", $output, $returnCode);

if ($returnCode === 0) {
    $passedTests++;
    printSuccess("Python executable found: " . implode(' ', $output));
} else {
    $failedTests++;
    printError("Python executable not found. Check PYTHON_PATH in .env");
}

// Check tajweed_analyzer.py
$totalTests++;
$analyzerPath = base_path('python/tajweed_analyzer.py');
if (file_exists($analyzerPath)) {
    $passedTests++;
    printSuccess("Tajweed analyzer script found");
} else {
    $failedTests++;
    printError("Tajweed analyzer script missing: {$analyzerPath}");
}

// Check requirements.txt
$totalTests++;
$requirementsPath = base_path('python/requirements.txt');
if (file_exists($requirementsPath)) {
    $passedTests++;
    printSuccess("Python requirements.txt found");
    printInfo("Verify dependencies: pip install -r python/requirements.txt");
} else {
    $failedTests++;
    printError("Python requirements.txt missing");
}

// ============================================================
// 6. QUEUE SYSTEM
// ============================================================
printHeader("6. Queue System");

// Check queue configuration
$totalTests++;
$queueDriver = env('QUEUE_CONNECTION', 'sync');
printInfo("Queue driver: {$queueDriver}");

if ($queueDriver === 'database') {
    try {
        $jobCount = DB::table('jobs')->count();
        $failedJobCount = DB::table('failed_jobs')->count();
        $passedTests++;
        printSuccess("Queue tables accessible ({$jobCount} pending, {$failedJobCount} failed)");
        
        if ($failedJobCount > 0) {
            printWarning("You have {$failedJobCount} failed jobs. Check with: php artisan queue:failed");
        }
    } catch (\Exception $e) {
        $failedTests++;
        printError("Queue tables not accessible: " . $e->getMessage());
    }
} else {
    $passedTests++;
    printWarning("Queue driver is '{$queueDriver}' (not using database queue)");
}

// ============================================================
// 7. ENVIRONMENT CONFIGURATION
// ============================================================
printHeader("7. Environment Configuration");

$envChecks = [
    'APP_ENV' => env('APP_ENV') ?? 'not set',
    'APP_DEBUG' => env('APP_DEBUG') ? 'true' : 'false',
    'APP_URL' => env('APP_URL') ?? 'not set',
    'DB_CONNECTION' => env('DB_CONNECTION') ?? 'not set',
    'DB_DATABASE' => env('DB_DATABASE') ?? 'not set',
    'MAIL_MAILER' => env('MAIL_MAILER') ?? 'not set',
];

foreach ($envChecks as $key => $value) {
    $totalTests++;
    if ($value !== 'not set' && $value !== null && $value !== '') {
        $passedTests++;
        printSuccess("{$key}: {$value}");
    } else {
        $failedTests++;
        printError("{$key} is not set");
    }
}

// Security checks
$totalTests++;
if (env('APP_ENV') === 'production' && env('APP_DEBUG') === true) {
    $failedTests++;
    printError("APP_DEBUG should be FALSE in production!");
} else {
    $passedTests++;
    printSuccess("Debug mode properly configured for environment");
}

// ============================================================
// 8. CACHE & OPTIMIZATION
// ============================================================
printHeader("8. Cache & Optimization");

// Check if routes are cached
$totalTests++;
if (file_exists(base_path('bootstrap/cache/routes-v7.php'))) {
    $passedTests++;
    printSuccess("Routes are cached");
} else {
    printWarning("Routes are not cached. Run: php artisan route:cache");
}

// Check if config is cached
$totalTests++;
if (file_exists(base_path('bootstrap/cache/config.php'))) {
    $passedTests++;
    printSuccess("Config is cached");
} else {
    printWarning("Config is not cached. Run: php artisan config:cache");
}

// Check if views are cached
$totalTests++;
$viewCachePath = storage_path('framework/views');
if (is_dir($viewCachePath) && count(glob($viewCachePath . '/*')) > 0) {
    $passedTests++;
    printSuccess("Views are compiled");
} else {
    printWarning("Views not compiled. They will compile on first access.");
}

// ============================================================
// SUMMARY
// ============================================================
$endTime = microtime(true);
$duration = round($endTime - $startTime, 2);

echo "\n" . Colors::$BOLD . "╔═══════════════════════════════════════════════════════╗\n";
echo "║                    TEST SUMMARY                       ║\n";
echo "╚═══════════════════════════════════════════════════════╝" . Colors::$RESET . "\n\n";

echo "Total Tests:   " . Colors::$BOLD . $totalTests . Colors::$RESET . "\n";
echo "Passed:        " . Colors::$GREEN . Colors::$BOLD . $passedTests . Colors::$RESET . "\n";
echo "Failed:        " . Colors::$RED . Colors::$BOLD . $failedTests . Colors::$RESET . "\n";
echo "Success Rate:  " . Colors::$BOLD . round(($passedTests / $totalTests) * 100, 1) . "%" . Colors::$RESET . "\n";
echo "Duration:      " . Colors::$BOLD . $duration . "s" . Colors::$RESET . "\n\n";

if ($failedTests === 0) {
    echo Colors::$GREEN . Colors::$BOLD . "✓ ALL SYSTEMS OPERATIONAL" . Colors::$RESET . "\n\n";
    exit(0);
} else {
    echo Colors::$RED . Colors::$BOLD . "✗ SOME SYSTEMS REQUIRE ATTENTION" . Colors::$RESET . "\n\n";
    printWarning("Please fix the failed checks above before deploying to production.");
    exit(1);
}
