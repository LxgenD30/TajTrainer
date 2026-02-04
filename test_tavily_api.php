<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use Illuminate\Support\Facades\Http;

echo "=== Testing Tavily API Search ===\n\n";

$apiKey = config('services.tavily.api_key');

if (!$apiKey) {
    echo "✗ ERROR: Tavily API key not found in config\n";
    exit(1);
}

echo "✓ API Key found: " . substr($apiKey, 0, 10) . "...\n\n";

echo "Sending test search request to Tavily API...\n";
echo "Query: 'Tajweed rules for beginners educational materials'\n\n";

// Simulate the actual controller code
$queryInput = 'Tajweed rules for beginners';
$fullQuery = $queryInput . ' educational materials learning resources';

echo "Full query sent to API: '$fullQuery'\n\n";

try {
    $response = Http::withoutVerifying()->withHeaders([
        'Authorization' => 'Bearer ' . $apiKey,
        'Content-Type' => 'application/json',
    ])->timeout(30)->post('https://api.tavily.com/search', [
        'query' => $fullQuery,
        'search_depth' => 'basic',
        'max_results' => 5,
        'include_images' => true,
        'topic' => 'general',
    ]);

    if ($response->successful()) {
        echo "✓ API Response received successfully!\n\n";
        
        $data = $response->json();
        
        if (isset($data['results']) && count($data['results']) > 0) {
            echo "✓ Found " . count($data['results']) . " results\n\n";
            
            echo "--- Sample Results ---\n\n";
            foreach (array_slice($data['results'], 0, 3) as $index => $result) {
                echo "Result " . ($index + 1) . ":\n";
                echo "  Title: " . ($result['title'] ?? 'N/A') . "\n";
                echo "  URL: " . ($result['url'] ?? 'N/A') . "\n";
                echo "  Content: " . substr($result['content'] ?? 'N/A', 0, 100) . "...\n";
                echo "  Score: " . ($result['score'] ?? 'N/A') . "\n";
                echo "\n";
            }
            
            echo "✓ Tavily API is working correctly!\n";
        } else {
            echo "⚠ No results returned from Tavily API\n";
        }
    } else {
        echo "✗ API Request failed with status: " . $response->status() . "\n";
        echo "Response: " . $response->body() . "\n";
    }
    
} catch (Exception $e) {
    echo "✗ ERROR: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n=== Test Complete ===\n";
