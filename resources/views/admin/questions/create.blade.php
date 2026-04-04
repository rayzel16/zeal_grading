@extends('admin.layouts.app')

@section('content')
<div class="container">
    @livewire('admin.question-form', ['exam' => $exam])
</div>
@endsection