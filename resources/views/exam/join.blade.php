@extends('layouts/app')

@section('content')
    <form method="POST" action="{{ route('exam.join') }}">
        @csrf
        <input type="text" name="code" placeholder="Enter Exam Code" required>
        <button class="btn btn-primary" type="submit">Join</button>
    </form>
@endsection
