<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

/**
 * robots.txt dan sitemap.xml yang dibangkitkan dari config/seo.php, supaya
 * URL sitemap selalu mengikuti APP_URL tanpa menyunting berkas statis.
 */
class SeoController extends Controller
{
    public function robots(): Response
    {
        $baris = ['User-agent: *'];

        if (app()->isProduction()) {
            $baris[] = 'Allow: /';

            foreach (config('seo.disallow', []) as $path) {
                $baris[] = 'Disallow: '.$path;
            }
        } else {
            // Local/staging: jangan diindeks sama sekali.
            $baris[] = 'Disallow: /';
        }

        $baris[] = '';
        $baris[] = 'Sitemap: '.url('sitemap.xml');

        return response(implode("\n", $baris)."\n", 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }

    public function sitemap(): Response
    {
        $xml = Cache::remember('seo.sitemap', now()->addHour(), function (): string {
            $lastmod = date('Y-m-d', (int) filemtime(resource_path('views/publik/beranda.blade.php')));
            $urls = '';

            foreach (config('seo.indexable_routes', []) as $i => $name) {
                $urls .= '<url><loc>'.e(route($name)).'</loc><lastmod>'.$lastmod.'</lastmod>'
                    .'<changefreq>weekly</changefreq><priority>'.($i === 0 ? '1.0' : '0.8').'</priority></url>';
            }

            return '<?xml version="1.0" encoding="UTF-8"?>'
                .'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'.$urls.'</urlset>';
        });

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }
}
