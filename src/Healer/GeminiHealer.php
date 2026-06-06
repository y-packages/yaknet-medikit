<?php

namespace YakNet\Medikit\Healer;

use GuzzleHttp\Client;
use YakNet\Medikit\Context\ErrorContext;
use YakNet\Medikit\Diagnosis;

/**
 * Uses Google Gemini AI to diagnose and heal PHP errors.
 */
class GeminiHealer
{
    private Client $client;

    public function __construct(
        private readonly string $apiKey,
        private readonly string $model = 'gemini-3.1-flash-lite'
    ) {
        $this->client = new Client([
            'base_uri' => 'https://generativelanguage.googleapis.com/v1beta/',
            'timeout'  => 10.0,
        ]);
    }

    public function diagnose(ErrorContext $context): Diagnosis
    {
        $prompt = $this->buildPrompt($context);

        try {
            $response = $this->client->post("models/{$this->model}:generateContent?key={$this->apiKey}", [
                'json' => [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'response_mime_type' => 'application/json',
                    ]
                ]
            ]);

            $body = $response->getBody()->getContents();
            $result = json_decode($body, true);
            if (!is_array($result)) {
                throw new \RuntimeException("Invalid response from Gemini API.");
            }

            /** @var array<int, array{content?: array{parts?: array<int, array{text?: string}>}}> $candidates */
            $candidates = $result['candidates'] ?? [];
            $rawText = $candidates[0]['content']['parts'][0]['text'] ?? null;
            if (!is_string($rawText)) {
                throw new \RuntimeException("Failed to extract content text from Gemini API response.");
            }

            $jsonResponse = json_decode($rawText, true);
            if (!is_array($jsonResponse)) {
                throw new \RuntimeException("Invalid JSON inside Gemini API response text.");
            }

            $explanation = $jsonResponse['explanation'] ?? 'No explanation provided.';
            $suggestedFix = $jsonResponse['suggested_fix'] ?? '';
            $confidenceRaw = $jsonResponse['confidence'] ?? 0.0;

            return new Diagnosis(
                explanation: is_string($explanation) ? $explanation : 'No explanation provided.',
                suggestedFix: is_string($suggestedFix) ? $suggestedFix : '',
                confidence: is_numeric($confidenceRaw) ? (float) $confidenceRaw : 0.0
            );

        } catch (\Throwable $e) {
            return new Diagnosis(
                explanation: "Failed to reach Gemini: " . $e->getMessage(),
                suggestedFix: ""
            );
        }
    }

    private function buildPrompt(ErrorContext $context): string
    {
        return <<<PROMPT
You are an expert PHP developer and diagnostic tool named YakNet Medikit.
Analyze the following PHP error and provide a fix.

ERROR MESSAGE: {$context->message}
FILE: {$context->file}
LINE: {$context->line}

CODE SNIPPET:
{$context->snippet}

Respond ONLY with a JSON object containing:
- "explanation": A brief, professional explanation of why the error happened.
- "suggested_fix": The corrected PHP code block or a specific one-liner to fix the issue.
- "confidence": A score between 0.0 and 1.0.

JSON:
PROMPT;
    }
}
