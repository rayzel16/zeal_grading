<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\RequestException;
use App\Services\Contracts\AIProvider;

class GeminiService implements AIProvider
{
    protected $apiKey;
    protected $endpoint;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.key');

        // Stable model
        $this->endpoint = "https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent";
    }

    /**
     * Generate Questions (MCQ, Essay, Identification)
     */
    public function generateQuestions($topic, $difficulty = 'medium', $count = 5, $type = 'multiple_choice')
    {
        // =========================
        // 🎯 Dynamic Format
        // =========================
        if ($type === 'multiple_choice') {
            $format = <<<FORMAT
[
    {
        "question": "...",
        "choices": ["A", "B", "C", "D"],
        "correct_index": 0,
        "explanation": "..."
    }
]
FORMAT;
        }

        if ($type === 'essay') {
            $format = <<<FORMAT
[
    {
        "question": "...",
        "expected_answer": "..."
    }
]
FORMAT;
        }

        if ($type === 'identification') {
            $format = <<<FORMAT
[
    {
        "question": "...",
        "expected_answer": "..."
    }
]
FORMAT;
        }

        // =========================
        // 🧠 Prompt
        // =========================
        $prompt = <<<PROMPT
Generate {$count} {$type} questions about {$topic}.
Difficulty: {$difficulty}

IMPORTANT:
- Return ONLY a valid JSON array
- No markdown, no backticks, no explanation outside JSON
- Ensure valid JSON (no trailing commas)

Format:
{$format}
PROMPT;

        try {
            $start = microtime(true);

            $response = Http::timeout(60)
                ->connectTimeout(10)
                ->retry(3, 2000)
                ->acceptJson()
                ->post($this->endpoint . '?key=' . $this->apiKey, [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt]
                            ]
                        ]
                    ]
                ]);

            Log::info('Gemini response time: ' . (microtime(true) - $start) . 's');

            if (!$response->successful()) {
                Log::error('Gemini API failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return null;
            }

            $data = $response->json();

            $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';

            if (!$text) {
                Log::error('Empty Gemini response', $data);
                return null;
            }

            // =========================
            // 🧹 Clean AI Output
            // =========================
            $text = preg_replace('/```json|```/', '', $text);
            $text = trim($text);

            // Extract JSON safely
            preg_match('/\[\s*{.*}\s*\]/s', $text, $matches);
            $jsonString = $matches[0] ?? null;

            if (!$jsonString) {
                Log::error('No JSON found in AI response', ['raw' => $text]);
                return null;
            }

            $decoded = json_decode($jsonString, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('Invalid AI JSON', [
                    'error' => json_last_error_msg(),
                    'json' => $jsonString
                ]);
                return null;
            }

            // =========================
            // 🔄 Normalize Data
            // =========================
            foreach ($decoded as &$item) {

                // Common
                $item['question'] = $item['question'] ?? '';

                if ($type === 'multiple_choice') {
                    $item['choices'] = $item['choices'] ?? [];

                    // Ensure 4 choices
                    while (count($item['choices']) < 4) {
                        $item['choices'][] = '';
                    }

                    $item['choices'] = array_slice($item['choices'], 0, 4);
                    $item['correct_index'] = $item['correct_index'] ?? 0;
                    $item['explanation'] = $item['explanation'] ?? '';
                }

                if (in_array($type, ['essay', 'identification'])) {
                    $item['expected_answer'] = $item['expected_answer'] ?? '';
                }
            }

            return $decoded;

        } catch (RequestException $e) {
            Log::error('Gemini Request Exception', [
                'message' => $e->getMessage(),
            ]);
            return null;

        } catch (\Exception $e) {
            Log::error('Gemini General Error', [
                'message' => $e->getMessage(),
            ]);
            return null;
        }
    }
}