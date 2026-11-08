
<?php
    $pageTitle = trim($__env->yieldContent('title'));
    $fullTitle = $pageTitle !== '' ? $pageTitle . ' — ' . $brand['name'] : $brand['name'] . ' — ' . $brand['tagline'];
    $metaDescription = trim($__env->yieldContent('meta_description')) ?: $brand['description'];
    $canonical = url()->current();
?>

<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
<meta name="theme-color" content="<?php echo e($brand['theme_color']); ?>" />

<title><?php echo e($fullTitle); ?></title>

<meta name="description" content="<?php echo e($metaDescription); ?>" />
<meta name="keywords" content="<?php echo e($brand['keywords']); ?>" />
<meta name="author" content="<?php echo e($brand['author']); ?>" />
<meta name="application-name" content="<?php echo e($brand['name']); ?>" />
<meta name="apple-mobile-web-app-title" content="<?php echo e($brand['short_name']); ?>" />
<meta name="robots" content="<?php echo e($brand['robots']); ?>" />
<meta name="referrer" content="strict-origin-when-cross-origin" />
<link rel="canonical" href="<?php echo e($canonical); ?>" />

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($brand['google_verification']): ?>
    <meta name="google-site-verification" content="<?php echo e($brand['google_verification']); ?>" />
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>


<meta property="og:type" content="website" />
<meta property="og:locale" content="<?php echo e(str_replace('-', '_', app()->getLocale()) === 'en' ? 'id_ID' : str_replace('-', '_', app()->getLocale())); ?>" />
<meta property="og:site_name" content="<?php echo e($brand['name']); ?>" />
<meta property="og:title" content="<?php echo e($fullTitle); ?>" />
<meta property="og:description" content="<?php echo e($metaDescription); ?>" />
<meta property="og:url" content="<?php echo e($canonical); ?>" />
<meta property="og:image" content="<?php echo e($brand['og_image_url']); ?>" />
<meta property="og:image:width" content="1200" />
<meta property="og:image:height" content="630" />
<meta property="og:image:alt" content="<?php echo e($brand['name']); ?>" />


<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:title" content="<?php echo e($fullTitle); ?>" />
<meta name="twitter:description" content="<?php echo e($metaDescription); ?>" />
<meta name="twitter:image" content="<?php echo e($brand['og_image_url']); ?>" />
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($brand['twitter']): ?>
    <meta name="twitter:site" content="<?php echo e(Str::start($brand['twitter'], '@')); ?>" />
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>


<link rel="icon" href="<?php echo e($brand['favicon_url']); ?>" sizes="any" />
<link rel="icon" type="image/png" sizes="32x32" href="<?php echo e($brand['favicon_png_url']); ?>" />
<link rel="apple-touch-icon" sizes="180x180" href="<?php echo e($brand['apple_icon_url']); ?>" />
<link rel="manifest" href="<?php echo e($brand['manifest_url']); ?>" />

<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>" />
<?php /**PATH C:\xampp\htdocs\myProject\o\o-saas\resources\views/partials/meta.blade.php ENDPATH**/ ?>