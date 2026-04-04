<?php

namespace App\Services\Contracts;

interface AIProvider
{
    public function generateQuestions($topic, $difficulty, $count);
}