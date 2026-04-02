document.addEventListener('DOMContentLoaded', () => {
    let choiceIndex = 1;

    const addBtn = document.getElementById('add-choice');
    const wrapper = document.getElementById('choices-wrapper');

    if (!addBtn || !wrapper) return;

    addBtn.addEventListener('click', function () {
        let div = document.createElement('div');
        div.classList.add('input-group', 'mb-2');

        div.innerHTML = `
            <div class="input-group-text">
                <input type="radio" name="correct_answer" value="${choiceIndex}">
            </div>
            <input type="text" name="choices[]" class="form-control" placeholder="Enter choice" required>
            <button type="button" class="btn btn-danger remove-choice">X</button>
        `;

        wrapper.appendChild(div);
        choiceIndex++;
    });

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-choice')) {
            e.target.parentElement.remove();
        }
    });
});