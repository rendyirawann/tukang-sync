<script>var hostUrl = "<?php echo e(asset('assets/')); ?>";</script>
<script src="<?php echo e(asset('assets/plugins/global/plugins.bundle.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/scripts.bundle.js')); ?>"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (window.toastr) {
            toastr.options = { closeButton: true, progressBar: true, positionClass: 'toastr-top-right', timeOut: 5000 };
            <?php if(session('success')): ?> toastr.success(<?php echo json_encode(session('success'), 15, 512) ?>); <?php endif; ?>
            <?php if(session('error')): ?> toastr.error(<?php echo json_encode(session('error'), 15, 512) ?>); <?php endif; ?>
            <?php if(session('warning')): ?> toastr.warning(<?php echo json_encode(session('warning'), 15, 512) ?>); <?php endif; ?>
            <?php if(session('info')): ?> toastr.info(<?php echo json_encode(session('info'), 15, 512) ?>); <?php endif; ?>
            <?php if($errors->any()): ?> toastr.error(<?php echo json_encode($errors->first(), 15, 512) ?>); <?php endif; ?>
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
<?php echo $__env->yieldPushContent('scripts'); ?>
<?php /**PATH C:\xampp\htdocs\myProject\o\o-saas\resources\views/tim/layout/scripts.blade.php ENDPATH**/ ?>