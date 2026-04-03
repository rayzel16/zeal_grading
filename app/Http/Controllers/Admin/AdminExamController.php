<?php

namespace App\Http\Controllers\Admin;

use App\Models\Exam;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminExamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $exams = \App\Models\Exam::latest()->get();
        return view('admin.exams.index', compact('exams'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.exams.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
         $request->validate([
        'title' => 'required',
        'duration' => 'required|integer|min:1',
        ]);

        \App\Models\Exam::create([
            'title' => $request->title,
            'description' => $request->description,
            'duration' => $request->duration,
            'code' => strtoupper(str()->random(6)),
            'max_attempts' => $request->max_attempts
        ]);

        return redirect()->route('admin.exams.index')
            ->with('success', 'Exam created!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $exam = \App\Models\Exam::findOrFail($id);

        return view('admin.exams.edit', compact('exam'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $exam = \App\Models\Exam::findOrFail($id);

        $request->validate([
            'title' => 'required',
            'duration' => 'required|integer|min:1',
            'max_attempts' => 'required|integer|min:1',
        ]);

        $exam->update([
            'title' => $request->title,
            'description' => $request->description,
            'duration' => $request->duration,
            'max_attempts' => $request->max_attempts
        ]);

        return redirect()->route('admin.exams.index')
        ->with('success', 'Exam updated!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Exam $exam)
     {
        $exam->delete();

        return redirect()->route('admin.exams.index')
            ->with('success', 'Exam deleted!');
    }
}
