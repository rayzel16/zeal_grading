@extends('layouts.app')

@section('content')
    <div class="container">
        <form method="POST" action="{{ route('exam.join') }}">
            @csrf

            <input type="text" name="code" placeholder="Enter Exam Code" required>

            <button type="submit" class="btn btn-primary">Join Exam</button>
        </form>
    </div>
@endsection