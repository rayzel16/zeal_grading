<!-- resources/views/student/exams/result.blade.php -->

<h2>Result</h2>

<p>Score: {{ $attempt->score }}</p>

@foreach($attempt->exam->questions as $index => $question)
    <div>
        <p><strong>Q{{ $index + 1 }}:</strong> {{ $question->question }}</p>

        @foreach($question->answers as $answer)
            @php
                $selected = $attempt->answers
                    ->where('question_id', $question->id)
                    ->first()?->answer_id;

                $isSelected = $selected == $answer->id;
            @endphp

            <p style="
                color:
                {{ $answer->is_correct ? 'green' : ($isSelected ? 'red' : 'black') }};
            ">
                {{ $answer->answer_text }}
            </p>
        @endforeach
    </div>
@endforeach