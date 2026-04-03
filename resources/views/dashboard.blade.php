@extends('layouts/app')
@section('content')
    <a href="{{ route('exam.join') }}">
        <button class="btn btn-primary mt-2">Join Exam</button>
    </a>

    <a href="{{ route('student.attempts') }}">
        <button class="btn btn-secondary mt-2">View Attempt History</button>
    </a>
@endsection