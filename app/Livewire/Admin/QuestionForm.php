<?php
namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Exam;

class QuestionForm extends Component
{
    public $exam;
    public $question = '';
    public $choices = [''];
    public $correct_answer = null;

    public function mount(Exam $exam)
    {
        $this->exam = $exam;
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


        $this->reset(['question', 'choices', 'correct_answer']);
        $this->choices = [''];

        session()->flash('success', 'Question saved!');
    }

    public function render()
    {
        return view('livewire.admin.question-form');
    }
}