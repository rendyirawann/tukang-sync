{{--
    Menandai isian yang gagal validasi (redirect back withErrors) tanpa harus
    menulis @error di setiap input: setiap kolom yang namanya ada di $errors
    diberi kelas is-invalid dan pesan di bawahnya. Formulir yang sudah
    menulis @error sendiri tidak digandakan.
--}}
@if (isset($errors) && $errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var galat = @json($errors->getMessages());
            Object.keys(galat).forEach(function (kunci) {
                // "items.0.koefisien" -> name="items[0][koefisien]"
                var bagian = kunci.split('.');
                var nama = bagian.shift() + bagian.map(function (b) { return '[' + b + ']'; }).join('');
                var el = document.querySelector('[name="' + nama + '"]') || document.querySelector('[name="' + nama + '[]"]');
                if (!el) { return; }
                el.classList.add('is-invalid');
                var induk = el.closest('.input-group') || el;
                var sudah = induk.parentElement && induk.parentElement.querySelector('.invalid-feedback');
                if (!sudah) {
                    var div = document.createElement('div');
                    div.className = 'invalid-feedback d-block';
                    div.textContent = galat[kunci][0];
                    induk.insertAdjacentElement('afterend', div);
                }
                // Formulir di dalam modal: buka modalnya supaya galat terlihat.
                var modal = el.closest('.modal');
                if (modal && window.bootstrap && !document.querySelector('.modal.show')) {
                    window.bootstrap.Modal.getOrCreateInstance(modal).show();
                }
            });
        });
    </script>
@endif
