@extends('admin.layouts.app')

@section('content')
<div class="container">
    <h2>Create Exam</h2>

    <form method="POST" action="{{ route('admin.exams.store') }}">
        @csrf

        <div class="mb-3">
            <label>Title</label>
            <input type="text" name="title" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Description</label>
            <textarea name="description" class="form-control"></textarea>
        </div>

        <div class="mb-3">
            <label>Duration (minutes)</label>
            <input type="number" name="duration" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Max Attempts</label>
            <input type="number" 
                name="max_attempts" 
                class="form-control" 
                value="1" 
                min="1" required>
        </div>

        <button class="btn btn-success">Save</button>
    </form>
</div>
@endsection