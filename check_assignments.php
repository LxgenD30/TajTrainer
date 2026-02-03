<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Assignment;
use App\Models\Classroom;

echo "=== Checking Assignments ===\n\n";

$assignments = Assignment::with('classroom')->get();
echo "Total assignments: " . $assignments->count() . "\n\n";

if ($assignments->count() > 0) {
    $assignment = $assignments->first();
    echo "Assignment structure:\n";
    echo json_encode($assignment->toArray(), JSON_PRETTY_PRINT) . "\n\n";
    
    // Check if classroom has assignments relationship
    $classroom = Classroom::with('assignments')->find($assignment->class_id);
    if ($classroom) {
        echo "Classroom: {$classroom->class_name}\n";
        echo "Assignments count: " . $classroom->assignments->count() . "\n";
    }
} else {
    echo "No assignments found in database\n";
}
