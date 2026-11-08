<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Memasang X-Robots-Tag: noindex, nofollow pada semua respons yang tidak
 * boleh diindeks mesin pencari.
 *
 * Yang boleh diindeks hanya halaman publik di config('seo.indexable_routes'),
 * berstatus 2xx, di APP_ENV=production. Panel admin, panel tim, halaman
 * login, API, dan halaman galat selalu noindex; di local/staging seluruh
 * situs noindex supaya tidak pernah muncul di hasil pencarian.
 */
class RobotsTag
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($response->headers->has('X-Robots-Tag')) {
            return $response;
        }

        $boleh = app()->isProduction()
            && $response->isSuccessful()
            && in_array($request->route()?->getName(), config('seo.indexable_routes', []), true);

        // robots.txt, sitemap, dan aset tidak perlu header ini.
        if ($request->is('robots.txt', 'sitemap.xml')) {
            return $response;
        }

        if (! $boleh) {
            $response->headers->set('X-Robots-Tag', 'noindex, nofollow');
        }

        return $response;
    }
}
