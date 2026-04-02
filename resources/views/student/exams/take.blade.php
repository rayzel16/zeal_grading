<h2>{{ $attempt->exam->title }}</h2>

<form method="POST" action="{{ route('exam.submit', $attempt) }}">
    @csrf

    @foreach($attempt->exam->questions as $index => $question)
        <div>
            <p><strong>Q{{ $index + 1 }}:</strong> {{ $question->question_text }}</p>

            @foreach($question->answers as $answer)
                <label>
                    <input type="radio"
                           name="answers[{{ $question->id }}]"
                           value="{{ $answer->id }}"
                           required>
                    {{ $answer->answer_text }}
                </label><br>
            @endforeach
        </div>
        <hr>
    @endforeach

    <button type="submit">Submit Exam</button>
</form>