@extends('admin.layouts.app')

@section('content')
<div class="container">
    <h2>Exams</h2>

    <div>
        <a href="{{ route('admin.exams.create') }}" class="btn btn-primary mb-3">
            Create Exam
        </a>
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Title</th>
                <th>Code</th>
                <th>Duration</th>
                <th>Attempts</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($exams as $exam)
            <tr>
                <td>{{ $exam->title }}</td>
                <td>{{ $exam->code }}</td>
                <td>{{ $exam->duration }} mins</td>
                <td>{{ $exam->max_attempts }}</td>
                <td>
                    <a href="{{ route('admin.exams.edit', $exam) }}" 
                    class="btn btn-warning btn-sm">Edit</a>

                    <form action="{{ route('admin.exams.destroy', $exam) }}" 
                        method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger btn-sm">Delete</button>
                    </form>

                    <a href="{{ route('admin.exams.questions.index', $exam) }}" 
                    class="btn btn-info btn-sm">
                    Questions
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection