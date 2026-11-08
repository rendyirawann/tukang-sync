{{--
    Penjaga sesi kedaluwarsa (419) untuk semua layout yang memuat jQuery/fetch.

    - Menyertakan X-CSRF-TOKEN otomatis pada AJAX jQuery dan fetch() same-origin.
    - Jawaban 419 dari AJAX ditampilkan sebagai pesan "Sesi kedaluwarsa"
      dengan tombol muat ulang, bukan kegagalan diam-diam.
    - Halaman yang dibiarkan terbuka lama: setelah mendekati umur sesi,
      pengguna diingatkan sebelum mengirim formulir panjang.
--}}
<script>
    (function () {
        var meta = document.querySelector('meta[name="csrf-token"]');
        var token = meta ? meta.getAttribute('content') : '';
        var pesan = 'Sesi kedaluwarsa, silakan muat ulang halaman.';

        function beriTahu() {
            if (window.Swal) {
                window.Swal.fire({
                    icon: 'warning',
                    title: 'Sesi kedaluwarsa',
                    text: pesan,
                    confirmButtonText: 'Muat ulang',
                    allowOutsideClick: false
                }).then(function () { window.location.reload(); });
            } else if (window.confirm(pesan)) {
                window.location.reload();
            }
        }

        if (window.jQuery) {
            window.jQuery.ajaxSetup({ headers: { 'X-CSRF-TOKEN': token } });
            window.jQuery(document).ajaxError(function (_e, xhr) {
                if (xhr && xhr.status === 419) { beriTahu(); }
            });
        }

        if (window.fetch) {
            var asli = window.fetch;
            window.fetch = function (input, init) {
                init = init || {};
                var url = typeof input === 'string' ? input : (input && input.url) || '';
                var sameOrigin = url.indexOf('http') !== 0 || url.indexOf(window.location.origin) === 0;
                if (sameOrigin) {
                    var headers = new Headers(init.headers || (typeof input !== 'string' && input.headers) || {});
                    if (!headers.has('X-CSRF-TOKEN')) { headers.set('X-CSRF-TOKEN', token); }
                    init.headers = headers;
                }
                return asli(input, init).then(function (res) {
                    if (res.status === 419) { beriTahu(); }
                    return res;
                });
            };
        }

        // Pengingat sebelum sesi habis (umur sesi dari config/session.php).
        var menit = {{ (int) config('session.lifetime', 120) }};
        setTimeout(function () {
            document.querySelectorAll('form[method="post"], form[method="POST"]').forEach(function (f) {
                f.addEventListener('submit', function (e) {
                    e.preventDefault();
                    beriTahu();
                }, { once: true });
            });
        }, Math.max(1, menit - 1) * 60 * 1000);
    })();
</script>
