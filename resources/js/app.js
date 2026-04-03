import './question-form';
import * as bootstrap from 'bootstrap';
import { initExamProctor } from './exam/take';

document.addEventListener('DOMContentLoaded', () => {

    const page = document.body.dataset.page ||
                 document.querySelector('[data-page]')?.dataset.page;

    if (page === 'exam-take') {

        const container = document.querySelector('[data-page="exam-take"]');

        initExamProctor(
            container.dataset.attemptId,
            container.dataset.csrfToken
        );
    }

});

window.bootstrap = bootstrap;