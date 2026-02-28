<?php

namespace App\Service;

class HuggingFaceService
{
    private string $apiKey;

    public function __construct(string $huggingfaceApiKey)
    {
        $this->apiKey = $huggingfaceApiKey;
    }

    public function generateDescription(string $eventName): string
    {
        $url = 'https://router.huggingface.co/v1/chat/completions';

        $payload = json_encode([
            'model' => 'Qwen/Qwen2.5-72B-Instruct',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => "Écris une description professionnelle et engageante en français pour cet événement agricole : \"$eventName\". La description doit faire 2 à 3 phrases, être informative et donner envie de participer. Réponds uniquement avec la description, sans introduction ni commentaire."
                ]
            ],
            'max_tokens' => 250,
            'temperature' => 0.7,
        ]);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $this->apiKey,
                'Content-Type: application/json',
            ],
            CURLOPT_TIMEOUT => 60,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            throw new \RuntimeException('Erreur de connexion : ' . $curlError);
        }

        $data = json_decode($response, true);

        if ($httpCode !== 200) {
            $errorMsg = $data['error']['message'] ?? $data['error'] ?? 'Erreur API Hugging Face (HTTP ' . $httpCode . ')';
            if (is_array($errorMsg)) {
                $errorMsg = json_encode($errorMsg);
            }
            throw new \RuntimeException($errorMsg);
        }

        if (isset($data['choices'][0]['message']['content'])) {
            $text = trim($data['choices'][0]['message']['content']);
            if (!empty($text)) {
                return $text;
            }
        }

        throw new \RuntimeException('Réponse inattendue de l\'API.');
    }
}
