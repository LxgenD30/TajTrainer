<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Auth;
use App\Services\ProgressTracker;

echo "=== Simulating Progress Page Load ===\n\n";

// Simulate logged in user
$userId = 2;

echo "Loading progress data for user_id: $userId\n\n";

try {
    $progressTracker = new ProgressTracker();
    
    // Simulate what the controller does
    echo "1. Getting overall progress (30 days)...\n";
    $overallProgress = $progressTracker->getUserProgress($userId, 30);
    echo "   ✓ Total attempts: {$overallProgress['total_attempts']}\n";
    echo "   ✓ Accuracy: {$overallProgress['accuracy']}%\n\n";
    
    echo "2. Getting daily progress (14 days)...\n";
    $dailyProgress = $progressTracker->getDailyProgress($userId, 14);
    echo "   ✓ Got " . count($dailyProgress) . " days of data\n\n";
    
    echo "3. Getting top weaknesses (5)...\n";
    $topWeaknesses = $progressTracker->getTopWeaknesses($userId, 5);
    echo "   ✓ Found " . count($topWeaknesses) . " weaknesses\n";
    foreach ($topWeaknesses as $w) {
        echo "      - {$w->rule_name}: {$w->error_count} errors\n";
    }
    echo "\n";
    
    echo "4. Getting improvement trends...\n";
    $improvementTrends = $progressTracker->getImprovementTrends($userId);
    echo "   ✓ Current week: {$improvementTrends['current_week_accuracy']}%\n";
    echo "   ✓ Previous week: {$improvementTrends['previous_week_accuracy']}%\n";
    echo "   ✓ Change: {$improvementTrends['accuracy_change']}%\n\n";
    
    echo "5. Getting most improved (3)...\n";
    $mostImproved = $progressTracker->getMostImproved($userId, 3);
    echo "   ✓ Found " . count($mostImproved) . " improved rules\n\n";
    
    echo "6. Getting recurring errors (threshold=3)...\n";
    $recurringErrors = $progressTracker->getRecurringErrors($userId, 3);
    echo "   ✓ Found {$recurringErrors->count()} recurring errors\n\n";
    
    echo "✅ SUCCESS: All data loaded successfully!\n";
    echo "The progress page should display properly now.\n\n";
    
    // Show summary
    echo "=== Data Summary for View ===\n";
    echo "Variables passed to view:\n";
    echo "  - overallProgress: array with " . count($overallProgress) . " keys\n";
    echo "  - dailyProgress: array with " . count($dailyProgress) . " days\n";
    echo "  - topWeaknesses: collection with " . count($topWeaknesses) . " items\n";
    echo "  - improvementTrends: array with " . count($improvementTrends) . " keys\n";
    echo "  - mostImproved: array with " . count($mostImproved) . " items\n";
    echo "  - recurringErrors: collection with {$recurringErrors->count()} items\n";
    
} catch (\Exception $e) {
    echo "❌ ERROR: {$e->getMessage()}\n";
    echo "\nStack trace:\n{$e->getTraceAsString()}\n";
    exit(1);
}

echo "\n=== Test Complete ===\n";
