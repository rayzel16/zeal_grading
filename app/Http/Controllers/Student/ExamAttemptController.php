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
        $userId = Auth::id();

        $existing = ExamAttempt::where('user_id', $userId)
            ->where('exam_id', $exam->id)
            ->whereNull('submitted_at')
            ->first();

        if ($existing) {
            return redirect()->route('exam.take', $existing);
        }

        $attemptCount = ExamAttempt::where('user_id', $userId)
            ->where('exam_id', $exam->id)
            ->whereNotNull('submitted_at')
            ->count();

        if ($attemptCount >= $exam->max_attempts) {
            return back()->with('error', 'Max attempts reached.');
        }

        $attempt = ExamAttempt::create([
            'user_id' => $userId,
            'exam_id' => $exam->id,
            'started_at' => now()
        ]);

        return redirect()->route('exam.take', $attempt);
    }

    public function take(ExamAttempt $attempt)
    {

        if ($attempt->user_id !== Auth::id()) {
            abort(403);
        }

        if ($attempt->submitted_at) {
            return redirect()->route('exam.result', $attempt);
        }

        $attempt->load('exam.questions.answers');

        return view('student.exams.take', compact('attempt'));
    }

    public function submit(Request $request, ExamAttempt $attempt)
    {

        if ($attempt->user_id !== Auth::id()) {
            abort(403);
        }

        if ($attempt->submitted_at) {
            return redirect()->route('exam.result', $attempt);
        }

        if (!$request->has('answers')) {
            return back()->with('error', 'Please answer at least one question.');
        }

        foreach ($request->answers as $questionId => $answerId) {
            AttemptAnswer::updateOrCreate(
                [
                    'attempt_id' => $attempt->id,
                    'question_id' => $questionId,
                ],
                [
                    'answer_id' => $answerId,
                ]
            );
        }

     
        $score = $attempt->answers()
            ->whereHas('answer', function ($q) {
                $q->where('is_correct', true);
            })
            ->count();


        $attempt->update([
            'submitted_at' => now(),
            'score' => $score
        ]);

        return redirect()->route('exam.result', $attempt);
    }

    public function result(ExamAttempt $attempt)
    {
        if ($attempt->user_id !== Auth::id()) {
            abort(403);
        }

        $attempt->load('exam.questions.answers', 'answers.answer');

        return view('student.exams.result', compact('attempt'));
    }
}