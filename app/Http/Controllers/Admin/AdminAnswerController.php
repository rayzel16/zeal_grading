<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Question;

class AdminAnswerController extends Controller
{

    public function index(Question $question)
    {
        $answers = $question->answers;

        return view('admin.answers.index', compact('question', 'answers'));
    }

    public function create(Question $question)
    {
        return view('admin.answers.create', compact('question'));
    }

    public function store(Request $request, Question $question)
    {
        $request->validate([
            'answer' => 'required',
        ]);

        // Reset previous correct answers (only 1 allowed)
        if ($request->is_correct) {
            $question->answers()->update(['is_correct' => false]);
        }

        $question->answers()->create([
            'answer_text' => $request->answer,
            'is_correct' => $request->is_correct ? true : false,
        ]);

        return redirect()
            ->route('admin.questions.answers.index', $question)
            ->with('success', 'Answer added!');
    }
}
