<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Exam;
use App\Models\Attempt as ExamAttempt;
use App\Models\Question;
use App\Models\QuestionResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
        // 🔒 सुरक्षा: ensure owner
        if ($attempt->user_id !== Auth::id()) {
            abort(403);
        }

        // 📥 Get all answers
        $answers = $request->input('answers', []);

        foreach ($answers as $questionId => $value) {

            $question = Question::with('answers')->find($questionId);

            if (!$question) continue;

            $score = 0;
            $answerId = null;
            $answerText = null;

            // ================= MULTIPLE CHOICE =================
            if ($question->type === 'multiple_choice') {

                $answerId = $value;

                $selectedAnswer = $question->answers->firstWhere('id', $value);
                $score = $selectedAnswer && $selectedAnswer->is_correct ? 1 : 0;
            }

            // ================= IDENTIFICATION =================
            elseif ($question->type === 'identification') {

                $answerText = $value;

                $userAnswer = strtolower(trim($value));
                $correctAnswer = strtolower(trim($question->expected_answer));

                // exact match (you can upgrade later)
                $score = $userAnswer === $correctAnswer ? 1 : 0;
            }

            // ================= ESSAY =================
            elseif ($question->type === 'essay') {

                $answerText = $value;

                // not graded yet
                $score = null;
            }

            // ================= SAVE =================
            QuestionResponse::updateOrCreate(
                [
                    'attempt_id' => $attempt->id,
                    'question_id' => $questionId,
                ],
                [
                    'user_id' => Auth::id(),
                    'answer_id' => $answerId,
                    'answer_text' => $answerText,
                    'score' => $score
                ]
            );
        }

        $attempt->score = $attempt->responses()->sum('score');
        $attempt->save();

        return redirect()->route('exam.result', $attempt->id);
    }
    
    public function result(ExamAttempt $attempt)
    {
        if ($attempt->user_id !== Auth::id()) {
            abort(403);
        }

        $attempt->load('exam.questions.answers', 'responses.answer');

        return view('student.exams.result', compact('attempt'));
    }


    public function history(Request $request)
    {
        $query = ExamAttempt::with([
            'exam',
            'exam.questions',
            'exam.attempts' => function ($q) {
                $q->where('user_id', Auth::id())
                ->whereNotNull('submitted_at');
            }
        ])
        ->where('user_id', Auth::id())
        ->whereNotNull('submitted_at');

        if ($request->filled('exam_id')) {
            $query->where('exam_id', $request->exam_id);
        }

        if ($request->filled('date')) {
            $query->whereDate('submitted_at', $request->date);
        }

        $attempts = $query->orderByDesc('submitted_at')
            ->paginate(7)
            ->withQueryString();

        foreach ($attempts as $index => $attempt) {
            $attempt->attempt_number = $attempts->firstItem() + $index;
        }

        $bestScores = ExamAttempt::where('user_id', Auth::id())
            ->whereNotNull('submitted_at')
            ->selectRaw('exam_id, MAX(score) as best_score')
            ->groupBy('exam_id')
            ->pluck('best_score', 'exam_id');

        $exams = Exam::all();

        return view('student.exams.history', compact('attempts', 'exams', 'bestScores'));
    }


    public function logViolation(Request $request, ExamAttempt $attempt)
    {
        if ($attempt->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'type' => 'required|string',
            'data' => 'nullable|string'
        ]);

        $data = $request->data;

        if ($request->type === 'screenshot' && $data) {

            if (strlen($data) > 5_000_000) {
                return response()->json(['error' => 'Image too large'], 422);
            }

            $image = preg_replace('/^data:image\/\w+;base64,/', '', $data);
            $image = base64_decode($image);

            if ($image === false) {
                return response()->json(['error' => 'Invalid image'], 422);
            }

            $user = Auth::user();
            $filename = 'screenshots/' . $user->name . '_attempt_' . $attempt->id . '_' . Str::random(10) . '.png';

            Storage::disk('public')->put($filename, $image);

            $data = $filename;
        }

        $attempt->violations()->create([
            'type' => $request->type,
            'data' => $data
        ]);

        $violations = $this->getRealViolationCount($attempt);
        $limit = $attempt->exam->violation_limit;

        return response()->json([
            'count' => $violations,
            'limit' => $limit,
            'exceeded' => $violations >= $limit
        ]);
    }


    public function StudentDisplayViolation(ExamAttempt $attempt)
    {
        if ($attempt->user_id !== Auth::id()) {
            abort(403);
        }

        $violations = $attempt->violations()->where('type', '!=', 'screenshot')->get();

        return view('student.exams.violations', compact('attempt', 'violations'));
    }


    private function getRealViolationCount($attempt)
    {
        return $attempt->violations()
            ->where('type', '!=', 'screenshot')
            ->count();
    }
}