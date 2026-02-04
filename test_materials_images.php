<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$materials = App\Models\Material::with('items')->orderBy('created_at', 'desc')->take(5)->get();

if ($materials->count() > 0) {
    foreach ($materials as $material) {
        echo "Material #{$material->material_id}: {$material->title}\n";
        echo "Items: {$material->items->count()}\n";
        
        foreach ($material->items as $item) {
            $ext = pathinfo($item->path, PATHINFO_EXTENSION);
            echo "  - Item #{$item->item_id}: {$item->type} => {$item->path} (ext: {$ext})\n";
        }
        echo "---\n\n";
    }
} else {
    echo "No materials found\n";
}

// Check if any images exist
echo "\n=== Image Files Check ===\n";
$imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
$allItems = App\Models\MaterialItem::where('type', 'file')->get();
$imageCount = 0;
foreach ($allItems as $item) {
    $ext = strtolower(pathinfo($item->path, PATHINFO_EXTENSION));
    if (in_array($ext, $imageExtensions)) {
        $imageCount++;
        echo "Found image: {$item->path}\n";
    }
}
echo "Total images found: {$imageCount}\n";
