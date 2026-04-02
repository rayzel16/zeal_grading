<h2>{{ $exam->title }}</h2>
<p>{{ $exam->description }}</p>

<form method="POST" action="{{ route('exam.start', $exam) }}">
    @csrf
    <button>Start Exam</button>
</form>