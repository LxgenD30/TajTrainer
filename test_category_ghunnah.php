<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Http\Controllers\MaterialController;

echo "Testing AI Categorization - Ghunnah Detection\n";
echo "=============================================\n\n";

$controller = new MaterialController();
$reflection = new ReflectionClass($controller);
$method = $reflection->getMethod('categorizeMaterial');
$method->setAccessible(true);

// Test 1: Ghunnah keyword
echo "Test 1 - Title: 'Ghunnah Rules and Application'\n";
echo "        Desc: 'This covers nasal sound and merging'\n";
$result1 = $method->invoke($controller, 'Ghunnah Rules and Application', 'This covers nasal sound and merging');
echo "Result: " . $result1 . "\n";
echo "Expected: Idgham Bi Ghunnah\n";
echo "Status: " . ($result1 === 'Idgham Bi Ghunnah' ? '✓ PASS' : '✗ FAIL') . "\n\n";

// Test 2: Explicit Idgham Bi Ghunnah
echo "Test 2 - Title: 'Idgham with Ghunnah'\n";
echo "        Desc: 'Letters Ya, Noon, Meem, Waw'\n";
$result2 = $method->invoke($controller, 'Idgham with Ghunnah', 'Letters Ya, Noon, Meem, Waw');
echo "Result: " . $result2 . "\n";
echo "Expected: Idgham Bi Ghunnah\n";
echo "Status: " . ($result2 === 'Idgham Bi Ghunnah' ? '✓ PASS' : '✗ FAIL') . "\n\n";

// Test 3: Madd keywords
echo "Test 3 - Title: 'Madd Munfasil Rules'\n";
echo "        Desc: 'Elongation and lengthening duration'\n";
$result3 = $method->invoke($controller, 'Madd Munfasil Rules', 'Elongation and lengthening duration');
echo "Result: " . $result3 . "\n";
echo "Expected: Madd Rules\n";
echo "Status: " . ($result3 === 'Madd Rules' ? '✓ PASS' : '✗ FAIL') . "\n\n";

// Test 4: User's exact scenario
echo "Test 4 - Title: 'Ghunnah Resources'\n";
echo "        Desc: 'Educational materials about Ghunnah'\n";
$result4 = $method->invoke($controller, 'Ghunnah Resources', 'Educational materials about Ghunnah');
echo "Result: " . $result4 . "\n";
echo "Expected: Idgham Bi Ghunnah\n";
echo "Status: " . ($result4 === 'Idgham Bi Ghunnah' ? '✓ PASS' : '✗ FAIL') . "\n\n";

echo "=============================================\n";
echo "Check storage/logs/laravel.log for detailed AI responses\n";
