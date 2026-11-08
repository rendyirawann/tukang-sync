
<script>
    (function () {
        var KEY = 'data-bs-theme';
        var MIGRATED = 'app:theme-default-light';
        var mode = 'light';

        try {
            // Earlier builds forced dark on the sign-in screens, which left a
            // stale 'dark' in storage that would otherwise stick forever.
            // Clear it once; every explicit choice after that is respected.
            if (!localStorage.getItem(MIGRATED)) {
                localStorage.removeItem(KEY);
                localStorage.setItem(MIGRATED, '1');
            }

            var saved = localStorage.getItem(KEY);

            if (saved) {
                mode = saved;
            }

            if (mode === 'system') {
                mode = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            }
        } catch (e) {
            /* Private mode / storage disabled — light it is. */
        }

        document.documentElement.setAttribute('data-bs-theme', mode === 'dark' ? 'dark' : 'light');
    })();
</script>
<?php /**PATH C:\xampp\htdocs\myProject\o\o-saas\resources\views/partials/theme-boot.blade.php ENDPATH**/ ?>