window.addEventListener('load', function() {
    const form = document.querySelector('form[name=room]');
    if (form) {
        form.submitted = false;
        form.addEventListener('submit', function(e) {
            if (form.submitted) {
                e.preventDefault();
                return false;
            }
            form.submitted = true;
        });
    }
});