@extends('layouts.app')

@section('content')
<div class="container">

    <h2>Exam Violations</h2>

    <div class="mb-3">
        <strong>Exam:</strong> {{ $attempt->exam->title ?? 'N/A' }} <br>
        <strong>Attempt ID:</strong> {{ $attempt->id }} <br>
        <strong>Date:</strong> {{ $attempt->created_at->format('Y-m-d H:i') }}
    </div>

    <hr>

    @if($violations->isEmpty())
        <div class="alert alert-success">
            No violations detected ✅
        </div>
    @else

        <div class="alert alert-warning">
            Total Violations: <strong>{{ $violations->count() }}</strong>
        </div>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Type</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody>
                @foreach($violations as $index => $violation)
                    <tr>
                        <td>{{ $index + 1 }}</td>

                        <td>
                            <span class="badge bg-danger">
                                {{ strtoupper($violation->type) }}
                            </span>
                        </td>

                        <td>
                            {{ $violation->created_at->format('Y-m-d H:i:s') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    @endif

</div>
@endsection