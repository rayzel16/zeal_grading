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
        // 🧠 STRICT PROMPT
        // =========================
        $prompt = <<<PROMPT
    Generate {$count} {$type} questions about {$topic}.
    Difficulty: {$difficulty}

    STRICT RULES:
    - Return ONLY a valid JSON array
    - NO markdown, NO explanation outside JSON
    - Follow the format EXACTLY

    IMPORTANT:
    - If type is identification or essay:
    - DO NOT include choices
    - DO NOT include correct_index
    - DO NOT include explanation
    - MUST include "expected_answer"

    - If type is multiple_choice:
    - MUST include 4 choices
    - MUST include correct_index

    If rules are violated, output is INVALID.

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

            preg_match('/\[\s*{.*}\s*\]/s', $text, $matches);
            $jsonString = $matches[0] ?? null;

            if (!$jsonString) {
                Log::error('No JSON found in AI response', ['raw' => $text]);
                return null;
            }

            $decoded = json_decode($jsonString, true);

            Log::info('AI RAW DECODED', $decoded);

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

                $item['question'] = $item['question'] ?? '';

                // ================= MCQ =================
                if ($type === 'multiple_choice') {
                    $item['choices'] = $item['choices'] ?? [];

                    while (count($item['choices']) < 4) {
                        $item['choices'][] = '';
                    }

                    $item['choices'] = array_slice($item['choices'], 0, 4);
                    $item['correct_index'] = $item['correct_index'] ?? 0;
                    $item['explanation'] = $item['explanation'] ?? '';
                }

                // ================= ESSAY / IDENTIFICATION =================
                if (in_array($type, ['essay', 'identification'])) {

                    // 🔥 FIX: If AI wrongly returns MCQ → convert it
                    if (isset($item['choices'])) {
                        Log::warning('AI returned MCQ instead of identification/essay', $item);

                        $item['expected_answer'] =
                            $item['choices'][$item['correct_index']] ?? '';
                    }

                    // 🔥 Fallback mapping (handles AI inconsistency)
                    $item['expected_answer'] =
                        $item['expected_answer']
                        ?? $item['answer']
                        ?? $item['correct_answer']
                        ?? $item['solution']
                        ?? '';
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