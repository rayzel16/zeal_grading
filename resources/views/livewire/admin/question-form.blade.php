<div>
    <h2>Add Question</h2>

    @if (session()->has('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <!-- Question -->
    <div class="mb-3">
        <label>Question</label>
        <textarea wire:model="question" class="form-control"></textarea>
    </div>

    <!-- Choices -->
    <div class="mb-3">
        <label>Choices</label>

        @foreach($choices as $index => $choice)
            <div class="input-group mb-2">
                <div class="input-group-text">
                    <input type="radio" wire:model="correct_answer" value="{{ $index }}">
                </div>

                <input type="text"
                       wire:model="choices.{{ $index }}"
                       class="form-control">

                <button type="button"
                        wire:click="removeChoice({{ $index }})"
                        class="btn btn-danger">
                    X
                </button>
            </div>
        @endforeach

        <button type="button"
                wire:click="addChoice"
                class="btn btn-primary btn-sm">
            + Add Choice
        </button>
    </div>

    <button wire:click="save" class="btn btn-success">
        Save
    </button>
</div>