<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\ProgressTracker;

echo "=== Testing All ProgressTracker Methods ===\n\n";

$tracker = new ProgressTracker();
$userId = 2;

// 1. getUserProgress
echo "1. getUserProgress (30 days):\n";
$progress = $tracker->getUserProgress($userId, 30);
echo "   Total attempts: {$progress['total_attempts']}\n";
echo "   Accuracy: {$progress['accuracy']}%\n";
echo "   Assignment accuracy: {$progress['assignment_accuracy']}%\n";
echo "   Practice accuracy: {$progress['practice_accuracy']}%\n\n";

// 2. getTopWeaknesses
echo "2. getTopWeaknesses (5):\n";
try {
    $weaknesses = $tracker->getTopWeaknesses($userId, 5);
    foreach ($weaknesses as $weakness) {
        echo "   - {$weakness->rule_name}: {$weakness->error_count} errors, {$weakness->fail_rate}% fail rate\n";
    }
    echo "\n";
} catch (\Exception $e) {
    echo "   ERROR: {$e->getMessage()}\n\n";
}

// 3. getImprovementTrends
echo "3. getImprovementTrends:\n";
try {
    $trends = $tracker->getImprovementTrends($userId);
    echo "   Current week: {$trends['current_week_accuracy']}% ({$trends['current_week_total']} attempts)\n";
    echo "   Previous week: {$trends['previous_week_accuracy']}% ({$trends['previous_week_total']} attempts)\n";
    echo "   Change: {$trends['accuracy_change']}%\n";
    echo "   Improving: " . ($trends['is_improving'] ? 'Yes' : 'No') . "\n\n";
} catch (\Exception $e) {
    echo "   ERROR: {$e->getMessage()}\n\n";
}

// 4. getDailyProgress
echo "4. getDailyProgress (7 days):\n";
try {
    $daily = $tracker->getDailyProgress($userId, 7);
    foreach ($daily as $day) {
        echo "   {$day['day_name']}: {$day['accuracy']}% ({$day['total']} attempts)\n";
    }
    echo "\n";
} catch (\Exception $e) {
    echo "   ERROR: {$e->getMessage()}\n\n";
}

// 5. getMostImproved
echo "5. getMostImproved (3):\n";
try {
    $improved = $tracker->getMostImproved($userId, 3);
    if (count($improved) > 0) {
        foreach ($improved as $rule) {
            echo "   - {$rule->rule_name}: {$rule->old_accuracy}% -> {$rule->new_accuracy}% (+{$rule->improvement}%)\n";
        }
    } else {
        echo "   No improvement data available (need more historical data)\n";
    }
    echo "\n";
} catch (\Exception $e) {
    echo "   ERROR: {$e->getMessage()}\n\n";
}

// 6. getRecurringErrors
echo "6. getRecurringErrors (threshold=3):\n";
try {
    $recurring = $tracker->getRecurringErrors($userId, 3);
    if ($recurring->count() > 0) {
        foreach ($recurring as $error) {
            echo "   - {$error->rule_name}: {$error->occurrences} times\n";
        }
    } else {
        echo "   No recurring errors found\n";
    }
    echo "\n";
} catch (\Exception $e) {
    echo "   ERROR: {$e->getMessage()}\n\n";
}

// 7. getClassStats (test with class_id=1)
echo "7. getClassStats (class_id=1):\n";
try {
    $classStats = $tracker->getClassStats(1);
    echo "   Total students: {$classStats['total_students']}\n";
    echo "   Active students: {$classStats['active_students']}\n";
    echo "   Class average: {$classStats['class_average_accuracy']}%\n";
    echo "   Total practice sessions: {$classStats['total_practice_sessions']}\n";
    echo "   Common errors:\n";
    foreach ($classStats['common_errors'] as $error) {
        echo "      - {$error->rule_name}: {$error->total_errors} errors ({$error->student_count} students)\n";
    }
    echo "\n";
} catch (\Exception $e) {
    echo "   ERROR: {$e->getMessage()}\n\n";
}

echo "=== All Tests Complete ===\n";
