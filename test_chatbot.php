<?php
// Simple script to test the chatbot API endpoint

$url = 'http://localhost:8000/api/chatbot/message';
$data = [
    'message' => 'Quels produits avez-vous ?',
    'role' => 'client'
];

$options = [
    'http' => [
        'header'  => "Content-type: application/json\r\n",
        'method'  => 'POST',
        'content' => json_encode($data),
        'ignore_errors' => true
    ]
];

$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);

if ($result === FALSE) {
    echo "ERROR: Failed to connect to the chatbot API. Is the server running?\n";
    exit(1);
}

echo "Response from Chatbot API:\n";
echo $result . "\n";

$response = json_decode($result, true);
if (isset($response['success']) && $response['success']) {
    echo "\nSUCCESS: Chatbot responded correctly!\n";
} else {
    echo "\nFAILURE: Chatbot returned an error or unexpected response.\n";
}
