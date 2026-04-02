@extends('admin.layouts.app')

@section('content')
    <h1>ADMIN AKO MWEHEHE</h1>

    <a href="{{ route('admin.exams.index') }}">
        <button class="btn btn-primary">Generate exams</button>
    </a>
@endsection