<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Verifying Database Schema ===\n\n";

// Check table structure
echo "tajweed_error_logs columns:\n";
$columns = DB::select('SHOW COLUMNS FROM tajweed_error_logs');
foreach ($columns as $col) {
    $key = $col->Key ? " ({$col->Key})" : '';
    $null = $col->Null === 'YES' ? ' NULL' : ' NOT NULL';
    echo "  - {$col->Field}: {$col->Type}{$null}{$key}\n";
}

echo "\n";

// Check foreign keys
echo "Foreign keys:\n";
$fks = DB::select("
    SELECT 
        CONSTRAINT_NAME,
        COLUMN_NAME,
        REFERENCED_TABLE_NAME,
        REFERENCED_COLUMN_NAME
    FROM information_schema.KEY_COLUMN_USAGE
    WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'tajweed_error_logs'
    AND REFERENCED_TABLE_NAME IS NOT NULL
");

foreach ($fks as $fk) {
    echo "  - {$fk->COLUMN_NAME} → {$fk->REFERENCED_TABLE_NAME}.{$fk->REFERENCED_COLUMN_NAME}\n";
}

echo "\n";

// Check sample data
echo "Sample data (first 3 records):\n";
$samples = DB::table('tajweed_error_logs')
    ->select('id', 'practice_session_id', 'assignment_submission_id', 'error_type', 'was_correct')
    ->limit(3)
    ->get();

foreach ($samples as $sample) {
    $session = $sample->practice_session_id ? "practice:{$sample->practice_session_id}" : "assignment:{$sample->assignment_submission_id}";
    $status = $sample->was_correct ? 'CORRECT' : 'ERROR';
    echo "  ID {$sample->id}: {$session} - {$sample->error_type} - {$status}\n";
}

echo "\n=== Schema Verification Complete ===\n";
