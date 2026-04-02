<form method="POST" action="{{ route('exam.join') }}">
    @csrf
    <input type="text" name="code" placeholder="Enter Exam Code" required>
    <button type="submit">Join</button>
</form>
