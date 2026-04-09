<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Exam;
use App\Services\AIService;

class QuestionForm extends Component
{
    public $exam;

    // =========================
    // MANUAL FORM
    // =========================
    public $question = '';
    public $choices = [''];
    public $correct_answer = null;
    public $expected_answer = '';
    public $type = 'multiple_choice';

    // =========================
    // AI
    // =========================
    public $topic = '';
    public $difficulty = 'medium';
    public $question_count = 5;
    public $generatedQuestions = [];

    // =========================
    // TYPE SWITCH HANDLER
    // =========================
    public function updatedType()
    {
        $this->reset([
            'question',
            'choices',
            'correct_answer',
            'expected_answer',
            ]);

        // Reinitialize defaults
        if ($this->type === 'multiple_choice') {
            $this->choices = ['', '', '', ''];
            $this->correct_answer = 0;
        }

        if (in_array($this->type, ['essay', 'identification'])) {
            $this->expected_answer = '';
        }
    }

    public function mount(Exam $exam)
    {
        $this->exam = $exam;
    }

    // =========================
    // SAVE MANUAL
    // =========================
    public function save()
    {
        $rules = [
            'question' => 'required',
            'type' => 'required'
        ];

        if ($this->type === 'multiple_choice') {
            $rules['choices'] = 'required|array|min:1';
            $rules['choices.*'] = 'required|string';
            $rules['correct_answer'] = 'required';
        }

        if (in_array($this->type, ['essay', 'identification'])) {
            $rules['expected_answer'] = 'required|string';
        }

        $this->validate($rules);

        $question = $this->exam->questions()->create([
            'question_text' => $this->question,
            'type' => $this->type,
            'expected_answer' => $this->expected_answer ?? null
        ]);

        // Multiple Choice Answers
        if ($this->type === 'multiple_choice') {
            foreach ($this->choices as $index => $choice) {
                $question->answers()->create([
                    'answer_text' => $choice,
                    'is_correct' => $this->correct_answer == $index
                ]);
            }
        }

        // Reset
        $this->reset([
            'question',
            'choices',
            'correct_answer',
            'expected_answer'
        ]);

        $this->choices = [''];
        $this->type = 'multiple_choice';
        $this->generatedQuestions = [];

        session()->flash('success', 'Question saved!');
    }

    // =========================
    // MANUAL CHOICES
    // =========================
    public function addChoice()
    {
        $this->choices[] = '';
    }

    public function removeChoice($index)
    {
        unset($this->choices[$index]);
        $this->choices = array_values($this->choices);
    }

    // =========================
    // AI GENERATE
    // =========================
    public function generateBulk(AIService $ai)
    {
        $this->validate([
            'question_count' => 'required|integer|min:1|max:20'
        ]);

        $results = $ai->generateQuestions(
            $this->topic ?: 'general knowledge',
            $this->difficulty,
            $this->question_count,
            $this->type
        );

        if (!$results) {
            session()->flash('error', 'AI generation failed.');
            return;
        }

        // 🔥 FORCE CLEAN STRUCTURE (IMPORTANT FIX)
        $clean = [];

        foreach ($results as $q) {

            $clean[] = [
                'question' => $q['question'] ?? '',
                'expected_answer' => $q['expected_answer']
                    ?? ($q['choices'][$q['correct_index']] ?? ''),
                'choices' => $q['choices'] ?? [],
                'correct_index' => $q['correct_index'] ?? 0,
                'explanation' => $q['explanation'] ?? '',
            ];
        }

        // 🔥 THIS LINE FIXES LIVEWIRE REACTIVITY
        $this->generatedQuestions = collect($clean)->toArray();
    }

    // =========================
    // APPROVE AI
    // =========================
    public function approveGenerated()
    {
        foreach ($this->generatedQuestions as $item) {

            if (empty($item['question'])) {
                continue;
            }

            $question = $this->exam->questions()->create([
                'question_text' => $item['question'],
                'type' => $this->type,
                'expected_answer' => $item['expected_answer'] ?? null
            ]);

            // Multiple Choice
            if ($this->type === 'multiple_choice' && !empty($item['choices'])) {
                foreach ($item['choices'] as $index => $choice) {
                    $question->answers()->create([
                        'answer_text' => $choice,
                        'is_correct' => ($item['correct_index'] == $index),
                        'explanation' => $item['explanation'] ?? null
                    ]);
                }
            }
        }

        $this->generatedQuestions = [];

        session()->flash('success', 'Questions saved!');
    }

    // =========================
    // OTHER ACTIONS
    // =========================
    public function regenerate(AIService $ai)
    {
        $this->generateBulk($ai);
    }

    public function discardGenerated()
    {
        $this->generatedQuestions = [];
    }

    public function removeGenerated($index)
    {
        unset($this->generatedQuestions[$index]);
        $this->generatedQuestions = array_values($this->generatedQuestions);
    }

    public function addGeneratedChoice($qIndex)
    {
        $this->generatedQuestions[$qIndex]['choices'][] = '';
    }

    // =========================
    // LOAD TO MANUAL (FIXED)
    // =========================
    public function loadToManual($index)
    {
        $item = $this->generatedQuestions[$index] ?? null;

        if (!$item) return;

        $this->question = $item['question'] ?? '';

        if ($this->type === 'multiple_choice') {
            $this->choices = $item['choices'] ?? [''];
            $this->correct_answer = $item['correct_index'] ?? 0;
        }

        if (in_array($this->type, ['essay', 'identification'])) {
            $this->expected_answer = $item['expected_answer'] ?? '';
        }

        session()->flash('success', 'Loaded into manual editor!');
    }

    public function render()
    {
        return view('livewire.admin.question-form');
    }
}