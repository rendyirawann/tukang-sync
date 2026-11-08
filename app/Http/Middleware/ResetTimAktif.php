<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Support\Tenancy\TimAktif;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Mengosongkan tim aktif di awal setiap request.
 *
 * TimAktif disimpan statis; di bawah Octane satu worker melayani banyak
 * request, sehingga tanpa ini request superadmin bisa mewarisi tim dari
 * request anggota tim sebelumnya dan melihat data yang tersaring keliru.
 */
class ResetTimAktif
{
    public function handle(Request $request, Closure $next): Response
    {
        TimAktif::reset();

        try {
            return $next($request);
        } finally {
            TimAktif::reset();
        }
    }
}
