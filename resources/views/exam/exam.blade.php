<form method="POST" action="{{ route('exam.submit', $attempt->id) }}">
    @csrf

    @foreach($attempt->exam->questions as $question)
        <p>{{ $question->question_text }}</p>

        @foreach($question->answers as $answer)
            <label>
                <input type="radio" name="answers[{{ $question->id }}]" value="{{ $answer->id }}">
                {{ $answer->answer_text }}
            </label><br>
        @endforeach
    @endforeach

    <button type="submit">Submit</button>
</form>
