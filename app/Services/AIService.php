<?php

namespace App\Services;

use App\Services\GeminiService;

class AIService
{
    protected $driver;

    public function __construct()
    {
        $default = config('services.ai.default');

        $this->driver = match ($default) {
            'gemini' => app(GeminiService::class),
            default => throw new \Exception("Unsupported AI driver")
        };
    }

    public function generateQuestions($topic, $difficulty, $count)
    {
        return $this->driver->generateQuestions($topic, $difficulty, $count);
    }
}
