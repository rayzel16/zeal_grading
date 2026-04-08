<div>

    <!-- SUCCESS / ERROR -->
    @if (session()->has('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <x-ai-loading />

    <!-- ================= AI GENERATOR ================= -->
    <div class="card p-3 mb-3">
        <h5>🤖 AI Generator</h5>

        <!-- TYPE -->
        <select wire:model="type" class="form-control mb-2">
            <option value="multiple_choice">Multiple Choice</option>
            <option value="essay">Essay</option>
            <option value="identification">Identification</option>
        </select>

        <input type="text" wire:model="topic" class="form-control mb-2" placeholder="Topic">

        <select wire:model="difficulty" class="form-control mb-2">
            <option value="easy">Easy</option>
            <option value="medium">Medium</option>
            <option value="hard">Hard</option>
        </select>

        <input type="number" wire:model="question_count" class="form-control mb-2">

        <button wire:click="generateBulk"
                wire:loading.attr="disabled"
                class="btn btn-primary">

            <span wire:loading.remove>🤖 Generate</span>
            <span wire:loading>⏳ Generating...</span>
        </button>
    </div>

    <!-- ================= AI PREVIEW ================= -->
    @if(!empty($generatedQuestions))
        <div class="card p-3 mb-3">

            <h5>✏️ Edit Before Saving</h5>

            @foreach($generatedQuestions as $qIndex => $q)
                <div class="border p-3 mb-3">

                    <!-- QUESTION -->
                    <textarea wire:model="generatedQuestions.{{ $qIndex }}.question"
                              class="form-control mb-2"></textarea>

                    <!-- ================= MULTIPLE CHOICE ================= -->
                    @if($type === 'multiple_choice')
                        @foreach($q['choices'] as $cIndex => $choice)
                            <div class="input-group mb-2">

                                <div class="input-group-text">
                                    <input type="radio"
                                           wire:model="generatedQuestions.{{ $qIndex }}.correct_index"
                                           value="{{ $cIndex }}">
                                </div>

                                <input type="text"
                                       wire:model="generatedQuestions.{{ $qIndex }}.choices.{{ $cIndex }}"
                                       class="form-control">
                            </div>
                        @endforeach

                        <button wire:click="addGeneratedChoice({{ $qIndex }})"
                                class="btn btn-sm btn-secondary mb-2">
                            + Add Choice
                        </button>

                        <!-- EXPLANATION -->
                        <textarea wire:model="generatedQuestions.{{ $qIndex }}.explanation"
                                  class="form-control mb-2"
                                  placeholder="Explanation"></textarea>
                    @endif

                    <!-- ================= ESSAY / IDENTIFICATION ================= -->
                    @if(in_array($type, ['essay', 'identification']))
                        <div class="mb-2">
                            <label>Expected Answer</label>
                            <textarea wire:model="generatedQuestions.{{ $qIndex }}.expected_answer"
                                      class="form-control"></textarea>
                        </div>
                    @endif

                    <!-- ACTIONS -->
                    <button wire:click="removeGenerated({{ $qIndex }})"
                            class="btn btn-sm btn-danger">
                        Remove
                    </button>

                    <button wire:click="loadToManual({{ $qIndex }})"
                            class="btn btn-sm btn-info">
                        ✍️ Edit in Manual Form
                    </button>

                </div>
            @endforeach

            <button wire:click="approveGenerated" class="btn btn-success">
                Approve & Save
            </button>

            <button wire:click="regenerate" class="btn btn-warning">
                Regenerate
            </button>

            <button wire:click="discardGenerated" class="btn btn-danger">
                Discard
            </button>
        </div>
    @endif

    <!-- ================= DIVIDER ================= -->
    <hr class="my-4">
    <h4>✍️ Create Manually</h4>

    <!-- ================= TYPE SELECT ================= -->
    <div class="mb-3">
        <label>Question Type</label>
        <select wire:model="type" class="form-control">
            <option value="multiple_choice">Multiple Choice</option>
            <option value="essay">Essay</option>
            <option value="identification">Identification</option>
        </select>
    </div>

    <!-- ================= QUESTION ================= -->
    <div class="mb-3">
        <label>Question</label>
        <textarea wire:model="question" class="form-control"></textarea>
    </div>

    <!-- ================= MULTIPLE CHOICE ================= -->
    @if($type === 'multiple_choice')
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
    @endif

    <!-- ================= ESSAY / IDENTIFICATION ================= -->
    @if(in_array($type, ['essay', 'identification']))
        <div class="mb-3">
            <label>Expected Answer</label>
            <textarea wire:model="expected_answer" class="form-control"></textarea>
        </div>
    @endif

    <!-- ================= SAVE ================= -->
    <button type="button" wire:click="save" class="btn btn-success">
        Save Question
    </button>

</div>