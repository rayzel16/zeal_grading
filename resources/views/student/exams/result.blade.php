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
        $selectedAnswer = $attempt->answers
            ->firstWhere('question_id', $question->id)?->answer_id;
    @endphp

    <div style="margin-bottom: 20px;">
        <p><strong>Q{{ $index + 1 }}:</strong> {{ $question->question_text }}</p>

        @foreach($question->answers as $answer)
            @php
                $isSelected = $selectedAnswer == $answer->id;
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
    </div>
@endforeach