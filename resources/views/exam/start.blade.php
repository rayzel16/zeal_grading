@extends('layouts/app')

@section('content')
<body data-page="exam-start">

<h2>{{ $exam->title }}</h2>
<p>{{ $exam->description }}</p>

<form method="POST" action="{{ route('exam.start', $exam) }}">
    @csrf
    <button type="submit" class="btn btn-primary">Start Exam</button>
</form>

</body>
@endsection