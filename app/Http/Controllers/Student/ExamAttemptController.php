<?php
namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Exam;
use App\Models\Attempt as ExamAttempt;
use App\Models\AttemptAnswer;
use Illuminate\Support\Facades\Auth;

class ExamAttemptController extends Controller
{
    public function start(Exam $exam)
    {
        // Prevent multiple attempts
        $existing = ExamAttempt::where('user_id', Auth::id())
            ->where('exam_id', $exam->id)
            ->first();

        if ($existing) {
            return redirect()->route('exam.take', $existing);
        }

        $attempt = ExamAttempt::create([
            'user_id' => Auth::id(),
            'exam_id' => $exam->id,
            'started_at' => now()
        ]);

        return redirect()->route('exam.take', $attempt);
    }

    public function take(ExamAttempt $attempt)
    {
        $attempt->load('exam.questions.answers');

        return view('student.exams.take', compact('attempt'));
    }

    public function submit(Request $request, ExamAttempt $attempt)
    {
        foreach ($request->answers as $questionId => $answerId) {
            AttemptAnswer::create([
                'attempt_id' => $attempt->id,
                'question_id' => $questionId,
                'answer_id' => $answerId,
            ]);
        }

        // Calculate score
        $score = 0;

        foreach ($attempt->answers()->with('answer')->get() as $attemptAnswer) {
            if ($attemptAnswer->answer->is_correct) {
                $score++;
            }
        }

        $attempt->update([
            'submitted_at' => now(),
            'score' => $score
        ]);

        return redirect()->route('exam.result', $attempt);
    }

    public function result(ExamAttempt $attempt)
    {
        $attempt->load('exam.questions.answers', 'answers.answer');

        return view('student.exams.result', compact('attempt'));
    }
}