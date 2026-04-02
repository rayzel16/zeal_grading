@extends('admin.layouts.app')

@section('content')
<div class="container">
    <h2>Add Answer</h2>

    <form method="POST" 
          action="{{ route('admin.questions.answers.store', $question) }}">
        @csrf

        <div class="mb-3">
            <label>Answer</label>
            <input type="text" name="answer" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>
                <input type="checkbox" name="is_correct">
                Mark as correct answer
            </label>
        </div>

        <button class="btn btn-success">Save</button>
    </form>
</div>
@endsection