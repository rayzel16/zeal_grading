<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Exam;

class AdminQuestionController extends Controller
{
    public function index(Exam $exam)
    {
        $questions = $exam->questions;

        return view('admin.questions.index', compact('exam', 'questions'));
    }

    public function create(Exam $exam)
    {
        return view('admin.questions.create', compact('exam'));
    }

    public function store(Request $request, Exam $exam)
    {
        $request->validate([
            'question' => 'required',
            'choices' => 'required|array|min:1',
            'choices.*' => 'required|string',
            'correct_answer' => 'required'
        ]);

        $question = $exam->questions()->create([
            'question_text' => $request->question
        ]);

        foreach ($request->choices as $index => $choice) {
            $question->answers()->create([
                'answer_text' => $choice,
                'is_correct' => $request->correct_answer == $index
            ]);
        }

        return redirect()
            ->route('admin.exams.questions.index', $exam)
            ->with('success', 'Question with answers saved!');
    }
}
