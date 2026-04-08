@extends('layouts.app')

@section('content')

<div 
    data-page="exam-take"
    data-attempt-id="{{ $attempt->id }}"
    data-csrf-token="{{ csrf_token() }}"
>

    <h2>Take Exam: {{ $attempt->exam->title }}</h2>

    <!-- 🔒 FULLSCREEN GATE -->
    <div id="fullscreenGate" style="text-align:center; margin-top:100px;">
        <h2>This exam requires fullscreen mode</h2>
        <button id="enterFullscreenBtn" class="btn btn-primary">
            Enter Fullscreen
        </button>
    </div>

    <!-- 📝 EXAM CONTENT -->
    <div id="examContent" style="display:none;">

        <div>
            <p><strong>Exam Instructions:</strong></p>
            <ul>
                <li>Answer all questions to the best of your ability.</li>
                <li>Do not refresh the page or navigate away during the exam.</li>
                <li>Any violation of exam rules may result in disqualification.</li>
                <li>Good luck and have fun!</li>
            </ul>
        </div>
        <hr>
        <br>

        <div id="warningBox" style="display:none; color:red; font-weight:bold;"></div>

        <form id="examForm" method="POST" action="{{ route('exam.submit', $attempt) }}">
            @csrf
            
            @foreach($attempt->exam->questions as $index => $question)
                <div class="mb-4">
                    <p><strong>Q{{ $index + 1 }}:</strong> {{ $question->question_text }}</p>

                    {{-- ================= MULTIPLE CHOICE ================= --}}
                    @if($question->type === 'multiple_choice')
                        @foreach($question->answers as $answer)
                            <div>
                                <label>
                                    <input type="radio"
                                        name="answers[{{ $question->id }}]"
                                        value="{{ $answer->id }}">
                                    {{ $answer->answer_text }}
                                </label>
                            </div>
                        @endforeach
                    @endif

                    {{-- ================= IDENTIFICATION ================= --}}
                    @if($question->type === 'identification')
                        <input type="text"
                            name="answers[{{ $question->id }}]"
                            class="form-control"
                            placeholder="Your answer here">
                    @endif

                    {{-- ================= ESSAY ================= --}}
                    @if($question->type === 'essay')
                        <textarea name="answers[{{ $question->id }}]"
                                class="form-control"
                                rows="4"
                                placeholder="Write your answer here..."></textarea>
                    @endif

                </div>
            @endforeach

            <button type="submit" class="btn btn-success">Submit Exam</button>
        </form>

    </div>

</div>

@endsection