<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    // `false` is the "not supplied" sentinel: passing :user="$maybeNull"
    // explicitly must render the anonymous placeholder, not the viewer.
    'user' => false,
    'size' => 36,
    'fontSize' => null,
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    // `false` is the "not supplied" sentinel: passing :user="$maybeNull"
    // explicitly must render the anonymous placeholder, not the viewer.
    'user' => false,
    'size' => 36,
    'fontSize' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $user = $user === false ? auth()->user() : $user;
    $url = $user?->avatar_url;
    $initials = $user?->initials ?? '?';
    $label = $user?->name ?? 'Sistem';
    $fontSize = $fontSize ?? max(10, (int) round($size * 0.38));
?>


<span <?php echo e($attributes->merge(['class' => 'app-avatar'])); ?>

      style="width: <?php echo e($size); ?>px; height: <?php echo e($size); ?>px; font-size: <?php echo e($fontSize); ?>px;"
      title="<?php echo e($label); ?>">
    <span aria-hidden="true"><?php echo e($initials); ?></span>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($url): ?>
        <img src="<?php echo e($url); ?>" alt="<?php echo e($label); ?>" loading="lazy" onerror="this.remove();" />
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</span>
<?php /**PATH C:\xampp\htdocs\myProject\o\o-saas\resources\views/components/avatar.blade.php ENDPATH**/ ?>