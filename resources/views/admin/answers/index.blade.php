@extends('admin.layouts.app')

@section('content')
<div class="container">
    <h2>Answers for Question</h2>

    <p><strong>{{ $question->question }}</strong></p>

    <a href="{{ route('admin.questions.answers.create', $question) }}" 
       class="btn btn-primary mb-3">
        Add Answer
    </a>

    <ul class="list-group">
        @foreach($answers as $answer)
            <li class="list-group-item">
                {{ $answer->answer_text }}

                @if($answer->is_correct)
                    <span class="badge bg-success">Correct</span>
                @endif
            </li>
        @endforeach
    </ul>
</div>
@endsection