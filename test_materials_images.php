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
            echo "  - Item #{$item->item_id}: {$item->type} => {$item->path}\n";
        }
        echo "---\n\n";
    }
} else {
    echo "No materials found\n";
}
