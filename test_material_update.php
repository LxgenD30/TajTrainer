<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Material;

// Update material with video link
$material = Material::find(2);
if ($material) {
    $material->video_link = 'https://www.youtube.com/watch?v=dQw4w9WgXcQ';
    $material->save();
    echo "✓ Updated material ID 2 with YouTube link\n";
    echo "Video Link: " . $material->video_link . "\n";
    
    // Test regex extraction
    if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/i', $material->video_link, $matches)) {
        $videoId = $matches[1];
        echo "✓ Extracted Video ID: $videoId\n";
        echo "✓ Thumbnail URL: https://img.youtube.com/vi/$videoId/maxresdefault.jpg\n";
    } else {
        echo "✗ Failed to extract video ID\n";
    }
} else {
    echo "Material not found\n";
}
