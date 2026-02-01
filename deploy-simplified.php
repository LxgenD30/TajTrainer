<?php
/**
 * TajTrainer V2 - Simplified Deployment Script for cPanel
 * Version: Direct Git Pull (No intermediate repository)
 * Place this file in your subdomain root directory
 * 
 * Security: Change the SECRET_TOKEN below!
 * Webhook URL: https://tajtrainer.tajweedflow.com/deploy.php?token=YOUR_SECRET_TOKEN
 */

// ============================================
// CONFIGURATION - CHANGE THESE VALUES!
// ============================================

// Security token - Fixed token for webhook authentication
define('SECRET_TOKEN', 'KVgdeJcmqv49sSuhnLHxjQ8GpNrP0ltR');

// Laravel application path - where your code is
define('LARAVEL_PATH', '/home/tajweedf/tajtrainer');

// Log file location
define('LOG_FILE', '/home/tajweedf/tajtrainer.com/deployment.log');

// Git branch to pull from
define('GIT_BRANCH', 'main');

// Enable maintenance mode during deployment
define('ENABLE_MAINTENANCE', true);

// Run database migrations automatically (BE CAREFUL!)
define('AUTO_MIGRATE', false);

// ============================================
// SECURITY CHECK
// ============================================

function logMessage($message) {
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents(LOG_FILE, "[$timestamp] $message\n", FILE_APPEND);
    echo "$message\n";
}

function executeCommand($command, $description) {
    logMessage("Executing: $description");
    logMessage("Command: $command");
    
    $output = [];
    $returnVar = 0;
    exec($command . ' 2>&1', $output, $returnVar);
    
    $outputStr = implode("\n", $output);
    logMessage("Output: $outputStr");
    
    if ($returnVar !== 0) {
        logMessage("ERROR: Command failed with return code $returnVar");
        return false;
    }
    
    logMessage("SUCCESS: $description completed");
    return true;
}

// Verify token
$providedToken = $_GET['token'] ?? '';
if ($providedToken !== SECRET_TOKEN) {
    http_response_code(403);
    logMessage("SECURITY: Unauthorized deployment attempt from IP: " . $_SERVER['REMOTE_ADDR']);
    die('Forbidden: Invalid token');
}

// Verify request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    die('Method Not Allowed');
}

// ============================================
// START DEPLOYMENT
// ============================================

logMessage("======================================");
logMessage("DEPLOYMENT STARTED");
logMessage("Triggered by: " . $_SERVER['REMOTE_ADDR']);
logMessage("======================================");

try {
    // Change to Laravel directory
    chdir(LARAVEL_PATH);
    
    // Step 1: Enable maintenance mode
    if (ENABLE_MAINTENANCE) {
        executeCommand('php artisan down', 'Enable maintenance mode');
    }
    
    // Step 2: Check if this is a git repository
    if (!is_dir(LARAVEL_PATH . '/.git')) {
        throw new Exception(
            "Git repository not initialized at " . LARAVEL_PATH . 
            "\nPlease initialize git first by running in cPanel Terminal:\n" .
            "cd " . LARAVEL_PATH . "\n" .
            "git init\n" .
            "git remote add origin https://github.com/yourusername/tajtrainer-v2.git\n" .
            "git fetch\n" .
            "git checkout main"
        );
    }
    
    // Step 3: Stash any local changes (to avoid conflicts)
    executeCommand('git stash', 'Stash local changes');
    
    // Step 4: Fetch latest changes
    executeCommand('git fetch origin', 'Fetch from remote');
    
    // Step 5: Pull changes
    if (!executeCommand('git pull origin ' . GIT_BRANCH, 'Pull latest changes')) {
        throw new Exception('Git pull failed');
    }
    
    // Step 6: Install/Update Composer dependencies
    if (file_exists(LARAVEL_PATH . '/composer.json')) {
        // Try to find composer
        $composerPaths = [
            '/usr/local/bin/composer',
            '/usr/bin/composer',
            'composer'
        ];
        
        $composerCmd = 'composer';
        foreach ($composerPaths as $path) {
            if (file_exists($path) || $path === 'composer') {
                $composerCmd = $path;
                break;
            }
        }
        
        executeCommand(
            $composerCmd . ' install --no-dev --optimize-autoloader --no-interaction',
            'Install Composer dependencies'
        );
    }
    
    // Step 7: Clear old Laravel caches
    executeCommand('php artisan config:clear', 'Clear config cache');
    executeCommand('php artisan route:clear', 'Clear route cache');
    executeCommand('php artisan view:clear', 'Clear view cache');
    executeCommand('php artisan cache:clear', 'Clear application cache');
    
    // Step 8: Run database migrations (if enabled)
    if (AUTO_MIGRATE) {
        logMessage("WARNING: Running database migrations...");
        executeCommand('php artisan migrate --force', 'Run database migrations');
    }
    
    // Step 9: Optimize Laravel for production
    executeCommand('php artisan config:cache', 'Cache configuration');
    executeCommand('php artisan route:cache', 'Cache routes');
    executeCommand('php artisan view:cache', 'Cache views');
    
    // Step 10: Set proper permissions
    executeCommand('chmod -R 755 ' . LARAVEL_PATH . '/storage', 'Set storage permissions');
    executeCommand('chmod -R 755 ' . LARAVEL_PATH . '/bootstrap/cache', 'Set cache permissions');
    
    // Step 11: Disable maintenance mode
    if (ENABLE_MAINTENANCE) {
        executeCommand('php artisan up', 'Disable maintenance mode');
    }
    
    // ============================================
    // DEPLOYMENT COMPLETE
    // ============================================
    
    logMessage("======================================");
    logMessage("DEPLOYMENT COMPLETED SUCCESSFULLY!");
    logMessage("======================================");
    
    http_response_code(200);
    echo json_encode([
        'status' => 'success',
        'message' => 'Deployment completed successfully',
        'timestamp' => date('Y-m-d H:i:s'),
    ]);
    
} catch (Exception $e) {
    // ============================================
    // DEPLOYMENT FAILED
    // ============================================
    
    logMessage("======================================");
    logMessage("DEPLOYMENT FAILED!");
    logMessage("Error: " . $e->getMessage());
    logMessage("======================================");
    
    // Try to bring site back up
    if (ENABLE_MAINTENANCE) {
        chdir(LARAVEL_PATH);
        executeCommand('php artisan up', 'Emergency: Disable maintenance mode');
    }
    
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Deployment failed: ' . $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s'),
    ]);
}
