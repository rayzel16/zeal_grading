@extends('layouts.app')

@section('content')
<div class="container">

    <h2 class="mb-4">Attempt History</h2>

    <!-- 🔍 Filters -->
    <form method="GET" class="row g-2 mb-4">
        <div class="col-md-4">
            <select name="exam_id" class="form-select">
                <option value="">All Exams</option>
                @foreach($exams as $exam)
                    <option value="{{ $exam->id }}"
                        {{ request('exam_id') == $exam->id ? 'selected' : '' }}>
                        {{ $exam->title }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <input type="date" name="date" value="{{ request('date') }}" class="form-control">
        </div>

        <div class="col-md-2">
            <button class="btn btn-primary w-100">Filter</button>
        </div>
    </form>

    @if($attempts->isEmpty())
        <div class="alert alert-info">No attempts yet.</div>
    @else
        <div class="card shadow-sm">
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Attempt</th>
                            <th>Exam</th>
                            <th>Used</th>
                            <th>Score</th>
                            <th>%</th>
                            <th>Date</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($attempts as $attempt)
                            @php
                                $total = $attempt->exam->questions->count();
                                $percentage = $total > 0 
                                    ? round(($attempt->score / $total) * 100) 
                                    : 0;

                                $usedAttempts = $attempt->exam->attempts->count();
                            @endphp

                            <tr>

                                <td>
                                    <span class="badge bg-secondary col-4">
                                        #{{ $attempt->attempt_number }}
                                    </span>
                                </td>

                                <td>{{ $attempt->exam->title }}</td>

                                <td>
                                    <span>
                                        {{ $usedAttempts }} / {{ $attempt->exam->max_attempts }}
                                    </span>
                                </td>

                                <td>
                                    <span>{{ $attempt->score }}</span>

                                    @if($attempt->score == ($bestScores[$attempt->exam_id] ?? null))
                                        <span class="badge bg-success">Best</span>
                                    @endif
                                </td>

                                <td>
                                    <span class="badge col-6
                                        {{ $percentage >= 75 ? 'bg-success' : 'bg-danger' }}">
                                        {{ $percentage }}%
                                    </span>
                                </td>

                                <td>
                                    {{ $attempt->submitted_at->format('M d, Y H:i') }}
                                </td>

                                <td>
                                    <a href="{{ route('exam.result', $attempt) }}" 
                                       class="btn btn-sm btn-outline-primary">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 📄 Pagination -->
        <div class="mt-3">
            {{ $attempts->links('pagination::bootstrap-5') }}
        </div>
    @endif

</div>
@endsection