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

        // ✅ Use stable model (avoid preview issues)
        $this->endpoint = "https://generativelanguage.googleapis.com/v1beta/models/gemini-3-flash-preview:generateContent";
    }

    public function generateQuestions($topic, $difficulty = 'medium', $count = 5)
    {
        // ✅ FIX: use HEREDOC instead of backticks
        $prompt = <<<PROMPT
            Generate {$count} multiple choice questions about {$topic}.
            Difficulty: {$difficulty}

            IMPORTANT:
            - Return ONLY a valid JSON array
            - No markdown, no backticks, no extra text
            - Ensure valid JSON (no trailing commas)

            Format:
            [
                {
                    "question": "...",
                    "choices": ["A", "B", "C", "D"],
                    "correct_index": 0,
                    "explanation": "..."
                }
            ]
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

            // ⏱ Log response time
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

            // ✅ Clean markdown if present
            $text = preg_replace('/```json|```/', '', $text);
            $text = trim($text);

            // ✅ Extract JSON safely (handles messy AI output)
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

            // ✅ Normalize structure
            foreach ($decoded as &$item) {
                $item['question'] = $item['question'] ?? '';
                $item['choices'] = $item['choices'] ?? [];

                // Ensure exactly 4 choices
                while (count($item['choices']) < 4) {
                    $item['choices'][] = '';
                }

                $item['choices'] = array_slice($item['choices'], 0, 4);

                $item['correct_index'] = $item['correct_index'] ?? 0;
                $item['explanation'] = $item['explanation'] ?? '';
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