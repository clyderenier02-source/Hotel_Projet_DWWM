document.addEventListener('turbo:load', () => {

    const stars      = document.querySelectorAll('.opinion__star');
    const noteInput  = document.getElementById('opinion_note');

    // On n'est pas sur la page opinion → on arrête
    if (!stars.length || !noteInput) return;

    const ratingText = document.getElementById('rating-text');
    const textarea   = document.getElementById('opinion_comment');
    const btnSubmit  = document.getElementById('btn-submit');
    const charCount  = document.getElementById('char-count');
    const charWrap   = document.getElementById('char-wrap');

    let current = 0;

    function checkReady() {
        const ready = current > 0 && textarea.value.trim().length > 0;
        btnSubmit.disabled = !ready;

        if (ready) {
            btnSubmit.classList.add('opinion__submit--ready');
            btnSubmit.textContent = 'Envoyer mon avis';
        } else {
            btnSubmit.classList.remove('opinion__submit--ready');
            btnSubmit.textContent = 'Complétez le formulaire';
        }
    }

    function highlightStars(value, type) {
        stars.forEach(s => {
            const v = parseInt(s.dataset.value);
            s.classList.remove('opinion__star--hovered', 'opinion__star--selected');

            if (type === 'hover'  && v <= value) s.classList.add('opinion__star--hovered');
            if (type === 'select' && v <= value) s.classList.add('opinion__star--selected');
        });
    }

    stars.forEach(star => {
        const val = parseInt(star.dataset.value);

        star.addEventListener('mouseenter', () => {
            highlightStars(val, 'hover');
            ratingText.textContent = '';
            ratingText.classList.remove('opinion__rating-label--empty');
        });

        star.addEventListener('mouseleave', () => {
            highlightStars(current, 'select');
            if (current === 0) {
                ratingText.textContent = 'Sélectionnez une note';
                ratingText.classList.add('opinion__rating-label--empty');
            } else {
                ratingText.textContent = '';
            }
        });

        star.addEventListener('click', () => {
            current = val;
            noteInput.value = val;
            highlightStars(current, 'select');
            ratingText.textContent = '';
            ratingText.classList.remove('opinion__rating-label--empty');
            checkReady();
        });
    });

    textarea.addEventListener('input', () => {
        charCount.textContent = textarea.value.length;
        charWrap.classList.toggle('opinion__counter--limit', textarea.value.length > 450);
        checkReady();
    });
});