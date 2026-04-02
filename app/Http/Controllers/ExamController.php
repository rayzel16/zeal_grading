<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Exam;

class ExamController extends Controller
{
    public function join(Request $request)
    {
        $request->validate([
            'code' => 'required'
        ]);

        $exam = Exam::where('code', $request->code)->first();

        if (!$exam) {
            return back()->withErrors([
                'code' => 'Invalid exam code'
            ]);
        }

        return view('exam.start', compact('exam'));
    }
}
