@extends('layouts.app')

@section('content')

<h2>Result</h2>

@php
    $total = $attempt->exam->questions->count();
    $score = $attempt->score;
    $percentage = $total > 0 ? round(($score / $total) * 100) : 0;
@endphp

<p><strong>Score:</strong> {{ $score }} / {{ $total }} ({{ $percentage }}%)</p>

<hr>

@foreach($attempt->exam->questions as $index => $question)

    @php
        $response = $attempt->responses->firstWhere('question_id', $question->id);
    @endphp

    <div style="margin-bottom: 20px;">
        <p><strong>Q{{ $index + 1 }}:</strong> {{ $question->question_text }}</p>

        {{-- ================= MULTIPLE CHOICE ================= --}}
        @if($question->type === 'multiple_choice')

            @foreach($question->answers as $answer)
                @php
                    $isSelected = $response?->answer_id == $answer->id;
                    $isCorrect = $answer->is_correct;
                @endphp

                <p style="
                    padding: 5px;
                    border-radius: 5px;
                    background-color:
                        {{ $isCorrect ? '#d4edda' : ($isSelected ? '#f8d7da' : '#f8f9fa') }};
                    color:
                        {{ $isCorrect ? 'green' : ($isSelected ? 'red' : 'black') }};
                ">
                    {{ $answer->answer_text }}

                    @if($isCorrect)
                        ✅
                    @elseif($isSelected)
                        ❌
                    @endif
                </p>
            @endforeach

        @endif

        {{-- ================= IDENTIFICATION ================= --}}
        @if($question->type === 'identification')

            @php
                $isCorrect = $response?->score == 1;
            @endphp

            <p style="
                padding: 8px;
                border-radius: 5px;
                background-color: {{ $isCorrect ? '#d4edda' : '#f8d7da' }};
                color: {{ $isCorrect ? 'green' : 'red' }};
            ">
                <strong>Your Answer:</strong> {{ $response?->answer_text ?? '-' }}
                {!! $isCorrect ? '✅' : '❌' !!}
            </p>

            <p><strong>Correct Answer:</strong> {{ $question->expected_answer }}</p>

        @endif

        {{-- ================= ESSAY ================= --}}
        @if($question->type === 'essay')

            <p><strong>Your Answer:</strong></p>
            <div style="background:#f8f9fa; padding:10px;">
                {{ $response?->answer_text ?? '-' }}
            </div>

            @if($response?->score !== null)
                <p><strong>Score:</strong> {{ $response->score }}</p>
                <p><strong>Feedback:</strong> {{ $response->feedback }}</p>
            @else
                <p style="
                    padding: 8px;
                    background-color: #fff3cd;
                    color: #856404;
                    border-radius: 5px;
                ">
                    ⏳ Essay answer submitted — awaiting review.
                </p>
            @endif

        @endif

    </div>

@endforeach

@endsection