<?php

namespace App\Service;

class ModerationService
{
    private const API_URL = 'https://commentanalyzer.googleapis.com/v1alpha1/comments:analyze';
    private const TOXICITY_THRESHOLD = 0.75;

    public function __construct(
        private string $apiKey = ''
    ) {
    }

    /**
     * Returns true if the given text is considered toxic by the Perspective API.
     * Falls back to false (fail-open) if API key is missing or the API is unreachable.
     */
    public function isToxic(string $text): bool
    {
        if (empty($this->apiKey) || $this->apiKey === 'your_key_here') {
            return false;
        }

        $text = trim(strip_tags($text));
        if (empty($text)) {
            return false;
        }

        $payload = json_encode([
            'comment' => ['text' => $text],
            'languages' => ['fr'],
            'requestedAttributes' => [
                'TOXICITY' => new \stdClass(),
            ],
        ]);

        $url = self::API_URL . '?key=' . urlencode($this->apiKey);

        $context = stream_context_create([
            'http' => [
                'method'  => 'POST',
                'header'  => "Content-Type: application/json\r\n",
                'content' => $payload,
                'timeout' => 5,
                'ignore_errors' => true,
            ],
        ]);

        try {
            $response = @file_get_contents($url, false, $context);
            if ($response === false) {
                return false;
            }

            $data = json_decode($response, true);

            $toxicityScore = $data['attributeScores']['TOXICITY']['summaryScore']['value'] ?? null;
            
            if ($toxicityScore === null) {
                return false;
            }

            return $toxicityScore >= self::TOXICITY_THRESHOLD;
        } catch (\Throwable $e) {
            // Fail-open: never break the forum because of a moderation error
            return false;
        }
    }

    /**
     * Returns the raw toxicity score (0.0 to 1.0), or null on failure.
     */
    public function getToxicityScore(string $text): ?float
    {
        if (empty($this->apiKey) || $this->apiKey === 'your_key_here') {
            return null;
        }

        $text = trim(strip_tags($text));
        if (empty($text)) {
            return null;
        }

        $payload = json_encode([
            'comment' => ['text' => $text],
            'languages' => ['fr'],
            'requestedAttributes' => [
                'TOXICITY' => new \stdClass(),
            ],
        ]);

        $url = self::API_URL . '?key=' . urlencode($this->apiKey);

        $context = stream_context_create([
            'http' => [
                'method'  => 'POST',
                'header'  => "Content-Type: application/json\r\n",
                'content' => $payload,
                'timeout' => 5,
                'ignore_errors' => true,
            ],
        ]);

        try {
            $response = @file_get_contents($url, false, $context);
            if ($response === false) {
                return null;
            }

            $data = json_decode($response, true);
            return $data['attributeScores']['TOXICITY']['summaryScore']['value'] ?? null;
        } catch (\Throwable $e) {
            return null;
        }
    }
}
