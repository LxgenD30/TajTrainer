<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Http\Controllers\MaterialController;

echo "Testing AI Categorization with 'Others' Category\n";
echo "================================================\n\n";

$controller = new MaterialController();
$reflection = new ReflectionClass($controller);
$method = $reflection->getMethod('categorizeMaterial');
$method->setAccessible(true);

// Test 1: Tajweed - Ghunnah
echo "Test 1 - Tajweed Content (Ghunnah)\n";
echo "Title: 'Ghunnah Rules'\n";
echo "Desc: 'Nasal sound in Quran recitation'\n";
$result1 = $method->invoke($controller, 'Ghunnah Rules', 'Nasal sound in Quran recitation');
echo "Result: " . $result1 . "\n";
echo "Expected: Idgham Bi Ghunnah\n";
echo "Status: " . ($result1 === 'Idgham Bi Ghunnah' ? '✓ PASS' : '✗ FAIL') . "\n\n";

// Test 2: Tajweed - Madd
echo "Test 2 - Tajweed Content (Madd)\n";
echo "Title: 'Madd Rules'\n";
echo "Desc: 'Elongation in Quran recitation'\n";
$result2 = $method->invoke($controller, 'Madd Rules', 'Elongation in Quran recitation');
echo "Result: " . $result2 . "\n";
echo "Expected: Madd Rules\n";
echo "Status: " . ($result2 === 'Madd Rules' ? '✓ PASS' : '✗ FAIL') . "\n\n";

// Test 3: Non-Tajweed - Islamic History
echo "Test 3 - Non-Tajweed Content (Islamic History)\n";
echo "Title: 'Life of Prophet Muhammad'\n";
echo "Desc: 'Biography and history of the Prophet'\n";
$result3 = $method->invoke($controller, 'Life of Prophet Muhammad', 'Biography and history of the Prophet');
echo "Result: " . $result3 . "\n";
echo "Expected: Others\n";
echo "Status: " . ($result3 === 'Others' ? '✓ PASS' : '✗ FAIL') . "\n\n";

// Test 4: Non-Tajweed - Quran Translation
echo "Test 4 - Non-Tajweed Content (Translation)\n";
echo "Title: 'Quran Translation English'\n";
echo "Desc: 'English translation of Quran verses'\n";
$result4 = $method->invoke($controller, 'Quran Translation English', 'English translation of Quran verses');
echo "Result: " . $result4 . "\n";
echo "Expected: Others\n";
echo "Status: " . ($result4 === 'Others' ? '✓ PASS' : '✗ FAIL') . "\n\n";

// Test 5: Non-Tajweed - Arabic Grammar
echo "Test 5 - Non-Tajweed Content (Grammar)\n";
echo "Title: 'Arabic Grammar Basics'\n";
echo "Desc: 'Learn Arabic grammar rules'\n";
$result5 = $method->invoke($controller, 'Arabic Grammar Basics', 'Learn Arabic grammar rules');
echo "Result: " . $result5 . "\n";
echo "Expected: Others\n";
echo "Status: " . ($result5 === 'Others' ? '✓ PASS' : '✗ FAIL') . "\n\n";

echo "================================================\n";
echo "Category System Summary:\n";
echo "  - Tajweed Rules: Madd, Idgham Bi/Billa Ghunnah\n";
echo "  - Others: Non-Tajweed Islamic education\n";
echo "  - Default on error: Others (most generic)\n";
echo "================================================\n";
