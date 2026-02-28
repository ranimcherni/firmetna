<?php

namespace App\Service;

class HuggingFaceService
{
    // Note: parameter name kept for backwards compatibility with services.yaml
    public function __construct(private string $huggingfaceApiKey)
    {
    }

    public function generateDescription(string $eventName): string
    {
        $apiKey = $this->huggingfaceApiKey;

        if (empty($apiKey) || $apiKey === 'your_huggingface_api_key_here') {
            throw new \RuntimeException('Clé API non configurée. Ajoutez votre clé Gemini dans HUGGINGFACE_API_KEY dans le fichier .env');
        }

        $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=' . $apiKey;

        $prompt = "Écris une description professionnelle et engageante en français pour cet événement agricole : \"$eventName\". "
            . "La description doit faire 2 à 3 phrases, être informative et donner envie de participer. "
            . "Réponds uniquement avec la description, sans introduction ni commentaire.";

        $payload = json_encode([
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
            'generationConfig' => [
                'maxOutputTokens' => 300,
                'temperature' => 0.7,
            ]
        ]);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
            ],
            CURLOPT_TIMEOUT => 30,
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
            $errorMsg = $data['error']['message'] ?? 'Erreur API Gemini (HTTP ' . $httpCode . ')';
            throw new \RuntimeException($errorMsg);
        }

        $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

        if (!$text || trim($text) === '') {
            throw new \RuntimeException('Réponse inattendue de l\'API Gemini.');
        }

        return trim($text);
    }
}
