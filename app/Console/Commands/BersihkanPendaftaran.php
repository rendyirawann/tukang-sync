<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\StatusPembayaran;
use App\Enums\StatusTim;
use App\Models\Team;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Menghapus pendaftaran yang tidak pernah dibayar.
 *
 * Tim berstatus "menunggu" memegang email pendaftar (unik) dan kode timnya.
 * Tanpa pembersihan, orang yang batal lalu ingin mendaftar ulang terkunci
 * oleh emailnya sendiri. Tim yang punya pembayaran diterima tidak pernah
 * disentuh, berapa pun umurnya.
 */
class BersihkanPendaftaran extends Command
{
    protected $signature = 'saas:bersihkan-pendaftaran
        {--hari=30 : Umur minimal pendaftaran yang dihapus}
        {--dry-run : Hanya tampilkan, jangan hapus}';

    protected $description = 'Hapus permanen pendaftaran tim yang tidak dibayar setelah N hari';

    public function handle(): int
    {
        $batas = now()->subDays(max(1, (int) $this->option('hari')));

        $tim = Team::query()
            ->where('status', StatusTim::Menunggu)
            ->where('created_at', '<', $batas)
            ->whereDoesntHave('payments', fn ($q) => $q->where('status', StatusPembayaran::Diterima))
            ->get();

        if ($tim->isEmpty()) {
            $this->info('Tidak ada pendaftaran kedaluwarsa.');

            return self::SUCCESS;
        }

        $this->table(['Kode', 'Nama', 'Email', 'Didaftarkan'], $tim->map(fn (Team $t) => [
            $t->slug, $t->nama, $t->email, $t->created_at->format('d-m-Y'),
        ]));

        if ($this->option('dry-run')) {
            $this->warn($tim->count().' pendaftaran akan dihapus (dry run).');

            return self::SUCCESS;
        }

        foreach ($tim as $t) {
            DB::transaction(function () use ($t) {
                Storage::disk('local')->deleteDirectory('bukti-bayar/'.$t->id);
                // Anggota, pembayaran, dan data tim ikut terhapus lewat
                // foreign key cascadeOnDelete.
                $t->forceDelete();
            });
        }

        $this->info($tim->count().' pendaftaran dihapus.');

        return self::SUCCESS;
    }
}
