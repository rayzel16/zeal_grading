@extends('admin.layouts.app')

@section('content')
<div class="container">
    <h2>Add Question</h2>

    <form method="POST" 
          action="{{ route('admin.exams.questions.store', $exam) }}">
        @csrf

        <!-- Question -->
        <div class="mb-3">
            <label>Question</label>
            <textarea name="question" class="form-control" required></textarea>
        </div>

        <!-- Choices -->
        <div class="mb-3">
            <label>Choices</label>

            <div id="choices-wrapper">
                <div class="input-group mb-2">
                    <div class="input-group-text">
                        <input type="radio" name="correct_answer" value="0">
                    </div>
                    <input type="text" name="choices[]" class="form-control" placeholder="Enter choice" required>
                    <button type="button" class="btn btn-danger remove-choice">X</button>
                </div>
            </div>

            <button type="button" id="add-choice" class="btn btn-primary btn-sm">
                + Add Choice
            </button>
        </div>

        <button class="btn btn-success">Save</button>
    </form>
</div>
@endsection