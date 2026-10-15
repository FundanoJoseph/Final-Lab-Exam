/**
 * Student Information System – small progressive-enhancement script.
 * (No external dependencies.)
 */
document.addEventListener('DOMContentLoaded', function () {
    // ---- Confirm dangerous actions (delete forms) ------------------
    document.querySelectorAll('form[data-confirm]').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!window.confirm(form.getAttribute('data-confirm'))) {
                event.preventDefault();
            }
        });
    });

    // ---- Toggle collapsible panels (e.g. "Add Academic Record") ----
    document.querySelectorAll('[data-toggle]').forEach(function (button) {
        button.addEventListener('click', function () {
            var target = document.getElementById(button.getAttribute('data-toggle'));
            if (target) {
                target.classList.toggle('open');
            }
        });
    });

    // ---- Highlight the active sidebar link -------------------------
    var path = window.location.pathname.replace(/\/+$/, '');
    document.querySelectorAll('.sidebar-nav .nav-link').forEach(function (link) {
        var href = link.getAttribute('href').replace(/\/+$/, '');
        if (href === path || (path !== '' && href !== '' && path.indexOf(href) === 0)) {
            link.classList.add('active');
        }
    });

    // ---- Auto-dismiss flash messages -------------------------------
    setTimeout(function () {
        document.querySelectorAll('.alert').forEach(function (alert) {
            alert.style.transition = 'opacity .5s ease';
            alert.style.opacity = '0';
            setTimeout(function () { alert.remove(); }, 500);
        });
    }, 6000);
});
