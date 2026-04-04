<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Exam;
use App\Services\AIService;

class QuestionForm extends Component
{
    public $exam;

    // Manual form
    public $question = '';
    public $choices = [''];
    public $correct_answer = null;

    // AI
    public $topic = '';
    public $difficulty = 'medium';
    public $question_count = 5;
    public $generatedQuestions = [];

    public function mount(Exam $exam)
    {
        $this->exam = $exam;
    }

    public function save()
    {
        $this->validate([
            'question' => 'required',
            'choices' => 'required|array|min:1',
            'choices.*' => 'required|string',
            'correct_answer' => 'required'
        ]);

        $question = $this->exam->questions()->create([
            'question_text' => $this->question
        ]);

        foreach ($this->choices as $index => $choice) {
            $question->answers()->create([
                'answer_text' => $choice,
                'is_correct' => $this->correct_answer == $index
            ]);
        }

        // Reset form
        $this->reset(['question', 'choices', 'correct_answer']);
        $this->choices = [''];

        session()->flash('success', 'Question saved!');
    }

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
    // AI GENERATE (PREVIEW)
    // =========================
    public function generateBulk(AIService $ai)
    {
        $this->validate([
            'question_count' => 'required|integer|min:1|max:20'
        ]);

        $results = $ai->generateQuestions(
            $this->topic ?: 'general knowledge',
            $this->difficulty,
            $this->question_count
        );

        if (!$results) {
            session()->flash('error', 'AI generation failed.');
            return;
        }

        $this->generatedQuestions = $results;
    }

    // =========================
    // APPROVE & SAVE
    // =========================
    public function approveGenerated()
    {
        foreach ($this->generatedQuestions as $item) {

            if (empty($item['question']) || empty($item['choices'])) {
                continue;
            }

            $question = $this->exam->questions()->create([
                'question_text' => $item['question']
            ]);

            foreach ($item['choices'] as $index => $choice) {
                $question->answers()->create([
                    'answer_text' => $choice,
                    'is_correct' => ($item['correct_index'] == $index),
                    'explanation' => $item['explanation'] ?? null
                ]);
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

    public function loadToManual($index)
    {
        $item = $this->generatedQuestions[$index] ?? null;

        if (!$item) return;

        $this->question = $item['question'] ?? '';
        $this->choices = $item['choices'] ?? [''];
        $this->correct_answer = $item['correct_index'] ?? 0;

        session()->flash('success', 'Loaded into manual editor!');
    }

    public function render()
    {
        return view('livewire.admin.question-form');
    }
}