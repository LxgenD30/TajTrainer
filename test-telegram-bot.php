<?php
/**
 * Quick Telegram Bot Test Script
 * Run: php test-telegram-bot.php
 */

require __DIR__.'/vendor/autoload.php';

// Load environment
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$token = $_ENV['TELEGRAM_BOT_TOKEN'] ?? null;

if (!$token) {
    die("❌ Error: TELEGRAM_BOT_TOKEN not found in .env file\n");
}

echo "🤖 Testing Telegram Bot...\n\n";

// Test 1: Get bot info
echo "1️⃣ Testing bot connection...\n";
$url = "https://api.telegram.org/bot{$token}/getMe";
$response = file_get_contents($url);
$data = json_decode($response, true);

if ($data['ok']) {
    echo "✅ Bot connected: @" . $data['result']['username'] . "\n";
    echo "   Name: " . $data['result']['first_name'] . "\n\n";
} else {
    die("❌ Bot connection failed: " . $data['description'] . "\n");
}

// Test 2: Get recent messages
echo "2️⃣ Getting recent messages...\n";
$url = "https://api.telegram.org/bot{$token}/getUpdates";
$response = file_get_contents($url);
$data = json_decode($response, true);

if (empty($data['result'])) {
    echo "⚠️  No messages yet. Please:\n";
    echo "   1. Open Telegram\n";
    echo "   2. Search for: @" . $data['result']['username'] ?? 'your_bot' . "\n";
    echo "   3. Send: /start\n";
    echo "   4. Run this script again\n\n";
} else {
    $latestMessage = end($data['result']);
    $chatId = $latestMessage['message']['chat']['id'] ?? null;
    $text = $latestMessage['message']['text'] ?? 'No text';
    $from = $latestMessage['message']['from']['first_name'] ?? 'Unknown';
    
    echo "✅ Found message from {$from}: {$text}\n";
    echo "   Chat ID: {$chatId}\n\n";
    
    // Test 3: Send a test message
    if ($chatId) {
        echo "3️⃣ Sending test message...\n";
        $url = "https://api.telegram.org/bot{$token}/sendMessage";
        
        $testMessage = "✅ *TajTrainer Bot Test Successful!*\n\n";
        $testMessage .= "🎉 Your bot is working!\n\n";
        $testMessage .= "To link your account:\n";
        $testMessage .= "`/link your_email@example.com`";
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, [
            'chat_id' => $chatId,
            'text' => $testMessage,
            'parse_mode' => 'Markdown',
        ]);
        $response = curl_exec($ch);
        curl_close($ch);
        
        $result = json_decode($response, true);
        if ($result['ok']) {
            echo "✅ Test message sent successfully!\n";
            echo "   Check your Telegram for the message.\n\n";
        } else {
            echo "❌ Failed to send message: " . $result['description'] . "\n\n";
        }
    }
}

echo "🎯 Test complete!\n";
echo "\n📝 Note: Full webhook testing requires ngrok or localtunnel.\n";
echo "   For now, you can test commands by simulating webhook calls.\n";
