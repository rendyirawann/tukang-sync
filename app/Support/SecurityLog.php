<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Pencatat event keamanan ke channel log "security" (harian, 30 hari).
 *
 * Hanya metadata yang dicatat: tidak pernah password, token, isi .env,
 * atau data pribadi lengkap. Email disamarkan sebagian supaya log tetap
 * bisa dipakai menelusuri serangan tanpa menjadi daftar email pelanggan.
 */
final class SecurityLog
{
    private function __construct() {}

    /**
     * @param  array<string, mixed>  $konteks
     */
    public static function catat(string $event, ?Request $request = null, array $konteks = [], string $level = 'info'): void
    {
        $request ??= request();

        try {
            Log::channel('security')->log($level, $event, array_merge([
                'ip' => $request?->ip(),
                'method' => $request?->method(),
                'path' => $request?->path(),
                'user_id' => $request?->user()?->getAuthIdentifier(),
                'user_agent' => mb_substr((string) $request?->userAgent(), 0, 200),
            ], $konteks));
        } catch (Throwable) {
            // Gagal menulis log tidak boleh menggagalkan request pengguna.
        }
    }

    /** "budi.santoso@demo.test" -> "bu***@demo.test" */
    public static function samarkanEmail(?string $email): ?string
    {
        if ($email === null || ! str_contains($email, '@')) {
            return $email === null ? null : mb_substr($email, 0, 2).'***';
        }

        [$nama, $domain] = explode('@', $email, 2);

        return mb_substr($nama, 0, 2).'***@'.$domain;
    }
}
