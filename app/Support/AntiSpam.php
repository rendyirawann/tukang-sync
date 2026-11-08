<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Throwable;

/**
 * Perlindungan formulir publik dari bot: honeypot, time-trap, dan
 * (opsional) Cloudflare Turnstile.
 *
 * Dipasang lewat komponen <x-honeypot /> di view dan AntiSpam::lolos() di
 * controller/FormRequest. Penolakan sengaja tidak menjelaskan alasannya,
 * supaya bot tidak belajar dari pesannya.
 */
final class AntiSpam
{
    /** Nama kolom jebakan; dipertahankan dari formulir pendaftaran lama. */
    public const HONEYPOT = 'situs_web';

    /** Kolom berisi waktu formulir dimuat (terenkripsi). */
    public const WAKTU = '_dimuat';

    private function __construct() {}

    /** Nilai terenkripsi untuk kolom time-trap. */
    public static function stempel(): string
    {
        return Crypt::encryptString((string) time());
    }

    /**
     * Apakah request lolos semua pemeriksaan anti-bot.
     */
    public static function lolos(Request $request): bool
    {
        if (filled($request->input(self::HONEYPOT))) {
            SecurityLog::catat('antispam.honeypot', $request);

            return false;
        }

        if (self::timeTrapAktif() && ! self::cukupLama($request)) {
            SecurityLog::catat('antispam.time_trap', $request);

            return false;
        }

        if (self::turnstileAktif() && ! self::turnstileValid($request)) {
            SecurityLog::catat('antispam.turnstile', $request);

            return false;
        }

        return true;
    }

    public static function timeTrapAktif(): bool
    {
        if (! config('security.antispam.time_trap', true)) {
            return false;
        }

        // Tes otomatis mengirim formulir dalam hitungan milidetik. Time-trap
        // hanya diuji bila tesnya sendiri menyalakannya lewat config.
        if (app()->runningUnitTests()) {
            return (bool) config('security.antispam.time_trap_saat_test', false);
        }

        return true;
    }

    public static function turnstileAktif(): bool
    {
        return (bool) config('security.turnstile.enabled')
            && filled(config('security.turnstile.site_key'))
            && filled(config('security.turnstile.secret_key'));
    }

    private static function cukupLama(Request $request): bool
    {
        $nilai = (string) $request->input(self::WAKTU, '');

        if ($nilai === '') {
            return false;
        }

        try {
            $dimuat = (int) Crypt::decryptString($nilai);
        } catch (Throwable) {
            return false;
        }

        $selisih = time() - $dimuat;

        // Batas atas: formulir yang dibiarkan terbuka seharian tetap sah,
        // tetapi stempel lama yang didaur ulang bot tidak.
        return $selisih >= (int) config('security.antispam.min_seconds', 3)
            && $selisih <= 86400;
    }

    private static function turnstileValid(Request $request): bool
    {
        $token = (string) $request->input('cf-turnstile-response', '');

        if ($token === '') {
            return false;
        }

        try {
            $hasil = Http::asForm()->timeout(5)->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                'secret' => config('security.turnstile.secret_key'),
                'response' => $token,
                'remoteip' => $request->ip(),
            ]);

            return (bool) $hasil->json('success', false);
        } catch (Throwable) {
            // Cloudflare tidak terjangkau: lebih baik menolak satu
            // pendaftaran daripada membuka pintu bagi bot.
            return false;
        }
    }
}
