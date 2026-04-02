@extends('admin.layouts.app')

@section('content')
<div class="container">
    <h2>Questions for: {{ $exam->title }}</h2>

    <a href="{{ route('admin.exams.questions.create', $exam) }}" 
       class="btn btn-primary mb-3">
        Add Question
    </a>

    <ul class="list-group">
        @foreach($questions as $question)
            <li class="list-group-item">
                {{ $question->question_text }}
            </li>

            <a href="{{ route('admin.questions.answers.index', $question) }}" 
                class="btn btn-sm btn-info">
                Answers
            </a>
        @endforeach
    </ul>
</div>
@endsection
