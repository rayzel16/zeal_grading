<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Exam;
use App\Models\Attempt;
use App\Models\Answer;
use App\Models\AttemptAnswer;
use Illuminate\Support\Facades\Auth;


class ExamController extends Controller
{
    public function join(Request $request)
    {
        $request->validate([
            'code' => 'required|exists:exams,code'
        ]);

        $exam = Exam::where('code', $request->code)->first();

        $attempt = Attempt::create([
            'user_id' => Auth::id(),
            'exam_id' => $exam->id,
            'started_at' => now()
        ]);

        return redirect()->route('exam.start', $attempt->id);
    }


    public function start($attemptId)
    {
        $attempt = Attempt::with('exam.questions.answers')->findOrFail($attemptId);

        return view('exam.start', compact('attempt'));
    }


    public function submit(Request $request, $attemptId)
    {
        $attempt = Attempt::findOrFail($attemptId);
        $score = 0;

        foreach ($request->answers as $questionId => $answerId) {

            $correct = Answer::where('question_id', $questionId)
                            ->where('is_correct', true)
                            ->first();

            if ($correct && $correct->id == $answerId) {
                $score++;
            }

            AttemptAnswer::create([
                'attempt_id' => $attempt->id,
                'question_id' => $questionId,
                'selected_answer_id' => $answerId
            ]);
        }

        $attempt->update([
            'score' => $score,
            'finished_at' => now()
        ]);

        return redirect()->route('exam.result', $attempt->id);
    }


    public function result($attemptId)
    {
        $attempt = Attempt::with('exam')->findOrFail($attemptId);

        return view('exam.result', compact('attempt'));
    }
}
