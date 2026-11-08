<?php

declare(strict_types=1);

namespace App\Support;

use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

/**
 * Pesan halaman galat web.
 *
 * Untuk 4xx, pesan yang ditulis developer lewat abort(403, '...') ditampilkan
 * karena memang ditujukan ke pengguna. Pesan bawaan framework (berbahasa
 * Inggris, mis. "The route x could not be found") diabaikan. Untuk 5xx
 * pesan exception TIDAK pernah ditampilkan: bisa memuat SQL atau path server.
 */
final class PesanGalat
{
    private const BAWAAN_FRAMEWORK = ['The route', 'No query results', 'The command', 'Unauthenticated', 'This action is unauthorized', 'CSRF token', 'Too Many', 'The GET method', 'The POST method', 'The PUT method', 'The PATCH method', 'The DELETE method'];

    public static function untuk(?Throwable $e, string $bawaan): string
    {
        if ($e === null) {
            return $bawaan;
        }

        $status = $e instanceof HttpExceptionInterface ? $e->getStatusCode() : 500;
        $pesan = trim($e->getMessage());

        if ($status >= 500 || $pesan === '') {
            return $bawaan;
        }

        foreach (self::BAWAAN_FRAMEWORK as $awal) {
            if (str_starts_with($pesan, $awal)) {
                return $bawaan;
            }
        }

        return $pesan;
    }

    /** Detik tunggu dari header Retry-After pada 429, atau null. */
    public static function retryAfter(?Throwable $e): ?int
    {
        if (! $e instanceof HttpExceptionInterface) {
            return null;
        }

        $nilai = $e->getHeaders()['Retry-After'] ?? null;

        return is_numeric($nilai) ? (int) $nilai : null;
    }
}
