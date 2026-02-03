<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Material;

echo "=== Materials Database Check ===\n\n";

$materials = Material::orderBy('created_at', 'desc')->limit(5)->get();

echo "Total materials: " . Material::count() . "\n\n";

if ($materials->count() > 0) {
    foreach ($materials as $material) {
        echo "ID: {$material->material_id}\n";
        echo "Title: {$material->title}\n";
        echo "Description: " . ($material->description ? substr($material->description, 0, 50) . '...' : 'None') . "\n";
        echo "Thumbnail: " . ($material->thumbnail ? $material->thumbnail : 'None') . "\n";
        echo "Video Link: " . ($material->video_link ? $material->video_link : 'None') . "\n";
        echo "File Path: " . ($material->file_path ? $material->file_path : 'None') . "\n";
        echo "Is Public: " . ($material->is_public ? 'Yes' : 'No') . "\n";
        echo "Created: {$material->created_at}\n";
        echo str_repeat('-', 60) . "\n\n";
    }
} else {
    echo "No materials found!\n";
}

// Test YouTube extraction
echo "\n=== Testing YouTube URL Extraction ===\n\n";
$testUrls = [
    'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
    'https://youtu.be/dQw4w9WgXcQ',
    'https://www.youtube.com/embed/dQw4w9WgXcQ',
    'https://www.youtube.com/v/dQw4w9WgXcQ',
];

foreach ($testUrls as $url) {
    if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/i', $url, $matches)) {
        $videoId = $matches[1];
        echo "✓ URL: $url\n";
        echo "  Video ID: $videoId\n";
        echo "  Thumbnail: https://img.youtube.com/vi/$videoId/maxresdefault.jpg\n\n";
    } else {
        echo "✗ Failed: $url\n\n";
    }
}
