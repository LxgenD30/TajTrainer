<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== Materials Table Structure ===\n\n";

$columns = DB::select('SHOW COLUMNS FROM materials');

foreach($columns as $column) {
    echo "Column: " . $column->Field . "\n";
    echo "  Type: " . $column->Type . "\n";
    echo "  Null: " . $column->Null . "\n";
    echo "  Default: " . ($column->Default ?? 'NULL') . "\n";
    echo "\n";
}

echo "\n=== Material Model Fillable ===\n";
$material = new App\Models\Material();
echo "Fillable: " . implode(', ', $material->getFillable()) . "\n";

echo "\n=== Testing Tavily API Config ===\n";
$tavilyKey = config('services.tavily.api_key');
if ($tavilyKey) {
    echo "✓ Tavily API Key is set: " . substr($tavilyKey, 0, 10) . "...\n";
} else {
    echo "✗ Tavily API Key is NOT set\n";
}

echo "\n=== All checks complete ===\n";
