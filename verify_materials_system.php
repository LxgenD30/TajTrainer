<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Material;
use App\Models\MaterialItem;

echo "=== Materials System Verification ===\n\n";

// Test 1: Check if tables exist
echo "1. Checking database tables...\n";
try {
    $materialsCount = Material::count();
    echo "   ✓ materials table exists - {$materialsCount} records\n";
} catch (Exception $e) {
    echo "   ✗ materials table error: " . $e->getMessage() . "\n";
}

try {
    $itemsCount = MaterialItem::count();
    echo "   ✓ material_items table exists - {$itemsCount} records\n";
} catch (Exception $e) {
    echo "   ✗ material_items table error: " . $e->getMessage() . "\n";
}

// Test 2: Check category column
echo "\n2. Checking category functionality...\n";
try {
    $categories = Material::select('category')->distinct()->get();
    echo "   ✓ Category column exists\n";
    echo "   Available categories:\n";
    foreach ($categories as $cat) {
        echo "     - " . ($cat->category ?? 'NULL') . "\n";
    }
} catch (Exception $e) {
    echo "   ✗ Category column error: " . $e->getMessage() . "\n";
}

// Test 3: Check relationships
echo "\n3. Checking model relationships...\n";
try {
    $material = Material::with('items')->first();
    if ($material) {
        $itemCount = $material->items->count();
        echo "   ✓ Material->items relationship works\n";
        echo "   Material: {$material->title} has {$itemCount} items\n";
    } else {
        echo "   - No materials found to test relationship\n";
    }
} catch (Exception $e) {
    echo "   ✗ Relationship error: " . $e->getMessage() . "\n";
}

// Test 4: Category distribution
echo "\n4. Category distribution:\n";
try {
    $distribution = Material::selectRaw('category, COUNT(*) as count')
        ->groupBy('category')
        ->get();
    
    foreach ($distribution as $dist) {
        $cat = $dist->category ?? 'Uncategorized';
        echo "   {$cat}: {$dist->count}\n";
    }
} catch (Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n";
}

// Test 5: Recent materials with items
echo "\n5. Recent materials (last 5):\n";
try {
    $recent = Material::with('items')
        ->orderBy('created_at', 'desc')
        ->take(5)
        ->get();
    
    foreach ($recent as $mat) {
        $itemCount = $mat->items->count();
        $category = $mat->category ?? 'No category';
        echo "   - {$mat->title}\n";
        echo "     Category: {$category}\n";
        echo "     Items: {$itemCount}\n";
        foreach ($mat->items as $item) {
            echo "       * {$item->type}: " . ($item->title ?? $item->path) . "\n";
        }
    }
} catch (Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n";
}

// Test 6: Storage directories
echo "\n6. Checking storage directories...\n";
$directories = [
    'storage/app/public/materials' => storage_path('app/public/materials'),
    'storage/app/public/thumbnails' => storage_path('app/public/thumbnails'),
];

foreach ($directories as $name => $path) {
    if (file_exists($path)) {
        $files = count(glob($path . '/*'));
        echo "   ✓ {$name} exists ({$files} files)\n";
    } else {
        echo "   ✗ {$name} does not exist\n";
    }
}

echo "\n=== Verification Complete ===\n";
