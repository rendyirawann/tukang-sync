<?php

declare(strict_types=1);

namespace App\Support\Tenancy;

use App\Enums\StatusTim;
use App\Models\Team;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

/**
 * Aturan kapan sebuah tim boleh dipakai, dipusatkan untuk API dan web.
 */
final class PenjagaTim
{
    private function __construct() {}

    /** Pesan penolakan bila tim tidak boleh masuk sama sekali. */
    public static function pesanTolak(?Team $tim): ?string
    {
        if ($tim === null || $tim->trashed()) {
            return 'Tim tidak ditemukan.';
        }

        return match ($tim->status) {
            StatusTim::Menunggu => 'Pembayaran tim ini belum dikonfirmasi. Silakan konfirmasi ke admin via WhatsApp.',
            StatusTim::Ditangguhkan => 'Akses tim ini sedang ditangguhkan. Hubungi admin.',
            default => null,
        };
    }

    public static function alasanTolak(?Team $tim): ?JsonResponse
    {
        $pesan = self::pesanTolak($tim);

        if ($pesan === null) {
            return null;
        }

        $kode = match ($tim?->status) {
            StatusTim::Menunggu => 'TEAM_PENDING',
            StatusTim::Ditangguhkan => 'TEAM_SUSPENDED',
            default => 'TEAM_NOT_FOUND',
        };

        return ApiResponse::error($pesan, 403, code: $kode);
    }

    public static function pesanHanyaBaca(Team $tim): string
    {
        return 'Masa langganan tim berakhir '.$tim->masaBerlakuLabel()
            .'. Data tetap bisa dilihat, tetapi untuk menambah atau mengubah data silakan perpanjang langganan.';
    }
}
