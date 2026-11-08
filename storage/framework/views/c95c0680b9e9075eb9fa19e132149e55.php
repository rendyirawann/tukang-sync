
<?php ($bid = 'bukti-' . ($id ?? 'x')); ?>
<div class="bukti-input" id="<?php echo e($bid); ?>">
    <label class="form-label">Bukti transfer</label>
    <div class="bukti-zone border border-dashed border-gray-400 rounded p-5 text-center bg-light" tabindex="0"
        style="cursor: text; min-height: 90px">
        <div class="bukti-hint text-muted fs-7">
            <i class="ki-outline ki-picture fs-2x d-block mb-2"></i>
            Klik di sini lalu tempel screenshot dengan <b>Ctrl+V</b>, atau pilih berkas PNG/JPG/PDF di bawah.
        </div>
        <div class="bukti-preview d-none">
            <img alt="Pratinjau bukti" class="mw-100 rounded mb-3" style="max-height: 260px" />
            <div><button type="button" class="btn btn-sm btn-light-danger bukti-hapus">Hapus gambar</button></div>
        </div>
    </div>
    <input type="hidden" name="bukti_data" class="bukti-data" />
    <input type="file" name="bukti" accept=".png,.jpg,.jpeg,.pdf" class="form-control form-control-sm mt-3" />
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['bukti'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="text-danger fs-7 mt-1"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>

<script>
    (function () {
        var root = document.getElementById(<?php echo json_encode($bid, 15, 512) ?>);
        if (!root || root.dataset.siap) return;
        root.dataset.siap = '1';
        var zone = root.querySelector('.bukti-zone'),
            data = root.querySelector('.bukti-data'),
            hint = root.querySelector('.bukti-hint'),
            prev = root.querySelector('.bukti-preview'),
            img = prev.querySelector('img'),
            file = root.querySelector('input[type=file]');

        function terlihat() { return root.offsetParent !== null; }

        function pasang(url) {
            data.value = url; img.src = url;
            prev.classList.remove('d-none'); hint.classList.add('d-none');
            file.value = '';
        }

        function tangani(e) {
            var items = (e.clipboardData || {}).items || [];
            for (var i = 0; i < items.length; i++) {
                if (items[i].type.indexOf('image') === 0) {
                    var reader = new FileReader();
                    reader.onload = function (ev) { pasang(ev.target.result); };
                    reader.readAsDataURL(items[i].getAsFile());
                    e.preventDefault();
                    return true;
                }
            }
            return false;
        }

        root.addEventListener('focusin', function () { window.__buktiAktif = root; });
        root.addEventListener('click', function () { window.__buktiAktif = root; });
        zone.addEventListener('paste', function (e) { tangani(e); e.stopPropagation(); });
        // Tempel di mana saja selama form ini terlihat (dan fokus tidak di kolom teks).
        document.addEventListener('paste', function (e) {
            var t = e.target;
            if (!terlihat() || (t && (t.tagName === 'INPUT' || t.tagName === 'TEXTAREA'))) return;
            // Beberapa kotak di satu halaman: hanya yang terakhir diklik (atau yang pertama).
            if ((window.__buktiAktif || root) !== root && window.__buktiAktif) return;
            if (!window.__buktiAktif && document.querySelector('.bukti-input') !== root) return;
            tangani(e);
        });

        root.querySelector('.bukti-hapus').addEventListener('click', function () {
            data.value = ''; img.removeAttribute('src');
            prev.classList.add('d-none'); hint.classList.remove('d-none');
        });

        file.addEventListener('change', function () {
            if (file.files.length) { data.value = ''; prev.classList.add('d-none'); hint.classList.remove('d-none'); }
        });
    })();
</script>
<?php /**PATH C:\xampp\htdocs\myProject\o\o-saas\resources\views/backend/saas/_bukti_input.blade.php ENDPATH**/ ?>