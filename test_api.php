<?php

require __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Dotenv\Dotenv;

$dotenv = new Dotenv();
$dotenv->loadEnv(__DIR__.'/.env');

$apiKey = $_ENV['PERSPECTIVE_API_KEY'] ?? '';

if (empty($apiKey) || $apiKey === 'your_key_here') {
    die("Error: No API key found in .env\n");
}

$text = "Tu es un idiot";

$payload = json_encode([
    'comment' => ['text' => $text],
    'languages' => ['fr'],
    'requestedAttributes' => [
        'TOXICITY' => new \stdClass(),
        'SPAM' => new \stdClass(),
    ],
]);

$url = 'https://commentanalyzer.googleapis.com/v1alpha1/comments:analyze?key=' . urlencode($apiKey);

$context = stream_context_create([
    'http' => [
        'method'  => 'POST',
        'header'  => "Content-Type: application/json\r\n",
        'content' => $payload,
        'timeout' => 5,
        'ignore_errors' => true,
    ],
]);

$response = @file_get_contents($url, false, $context);

echo "RAW RESPONSE FROM GOOGLE:\n";
echo $response . "\n";
