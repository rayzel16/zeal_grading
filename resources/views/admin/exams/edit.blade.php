@extends('admin.layouts.app')

@section('content')
<div class="container">
    <h2>Edit Exam</h2>

    <form method="POST" action="{{ route('admin.exams.update', $exam) }}">
        @csrf
        @method('PUT') {{-- IMPORTANT --}}

        <div class="mb-3">
            <label>Title</label>
            <input 
                type="text" 
                name="title" 
                class="form-control"
                value="{{ old('title', $exam->title) }}"
                required
            >
        </div>

        <div class="mb-3">
            <label>Description</label>
            <textarea 
                name="description" 
                class="form-control"
            >{{ old('description', $exam->description) }}</textarea>
        </div>

        <div class="mb-3">
            <label>Duration (minutes)</label>
            <input 
                type="number" 
                name="duration" 
                class="form-control"
                value="{{ old('duration', $exam->duration) }}"
                required
            >
        </div>

        <button class="btn btn-success">Update</button>
    </form>
</div>
@endsection