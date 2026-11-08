<?php

/*
|--------------------------------------------------------------------------
| SEO
|--------------------------------------------------------------------------
| Nilai bawaan untuk partial resources/views/partials/seo.blade.php,
| robots.txt, dan sitemap.xml. Pengaturan di panel admin (tabel settings)
| tetap boleh menimpa nama, deskripsi, dan keyword.
*/

return [

    'title' => env('APP_NAME', 'Tukangsync'),

    'tagline' => env('SEO_TAGLINE', 'RAB & proyek, sinkron satu tim'),

    'description' => env('APP_DESCRIPTION', 'Tukangsync: aplikasi catatan proyek konstruksi untuk tim.'),

    'keywords' => env('SEO_KEYWORDS', 'aplikasi RAB, catatan proyek konstruksi, kontraktor, mandor'),

    'og_image' => env('SEO_OG_IMAGE', 'assets/media/branding/og-image.png'),

    'author' => env('SEO_AUTHOR', 'Tukangsync'),

    'theme_color' => '#1E3A5F',

    /*
    | Halaman yang boleh diindeks mesin pencari (nama route, GET tanpa auth).
    | Semua halaman lain mendapat X-Robots-Tag: noindex, nofollow. Di luar
    | APP_ENV=production seluruh situs noindex supaya staging tidak terindeks.
    */
    'indexable_routes' => ['beranda', 'daftar'],

    /*
    | Path yang ditolak di robots.txt.
    */
    'disallow' => ['/admin', '/api', '/docs', '/*/login', '/*/dashboard', '/*/status'],
];
