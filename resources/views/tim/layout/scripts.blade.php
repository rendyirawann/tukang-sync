<script>var hostUrl = "{{ asset('assets/') }}";</script>
<script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
<script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>
    @include('partials.csrf-guard')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (window.toastr) {
            toastr.options = { closeButton: true, progressBar: true, positionClass: 'toastr-top-right', timeOut: 5000 };
            @if (session('success')) toastr.success(@json(session('success'))); @endif
            @if (session('error')) toastr.error(@json(session('error'))); @endif
            @if (session('warning')) toastr.warning(@json(session('warning'))); @endif
            @if (session('info')) toastr.info(@json(session('info'))); @endif
            @if ($errors->any()) toastr.error(@json($errors->first())); @endif
        }
        // Konfirmasi hapus untuk form ber-atribut data-confirm.
        document.querySelectorAll('form[data-confirm]').forEach(function (f) {
            f.addEventListener('submit', function (e) {
                if (!confirm(f.dataset.confirm)) { e.preventDefault(); }
            });
        });
        document.querySelectorAll('[data-copy]').forEach(function (b) {
            b.addEventListener('click', function () {
                navigator.clipboard && navigator.clipboard.writeText(b.dataset.copy);
                window.toastr && toastr.info('Disalin: ' + b.dataset.copy);
            });
        });
    });
</script>
@stack('scripts')
