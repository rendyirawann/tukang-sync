<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\ActivityLog;
use App\Models\Kegiatan;
use App\Models\Lampiran;
use App\Models\Team;
use App\Support\Tenancy\TimAktif;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Mengosongkan seluruh proyek milik SATU tim.
 *
 * Versi aplikasi tunggal dulu men-TRUNCATE tabel; di SaaS itu akan
 * menghapus data semua pelanggan, jadi sekarang kode tim wajib disebut dan
 * penghapusan berjalan lewat model yang tersaring ke tim tersebut. RAB, kas,
 * bahan baku, lampiran, dan log harian ikut terhapus lewat foreign key
 * cascade. Akun, katalog, dan data master tim dipertahankan.
 */
class BersihkanKegiatan extends Command
{
    use ConfirmableTrait;

    protected $signature = 'kegiatan:bersihkan
        {tim : Kode tim yang proyeknya dikosongkan}
        {--aktivitas : Ikut menghapus jejak aktivitas tim tersebut}
        {--force : Jalankan tanpa konfirmasi}';

    protected $description = 'Mengosongkan seluruh proyek satu tim; akun, katalog, dan data master dipertahankan';

    public function handle(): int
    {
        $tim = Team::where('slug', Team::normalkanSlug((string) $this->argument('tim')))->first();

        if ($tim === null) {
            $this->components->error('Kode tim tidak ditemukan.');

            return self::FAILURE;
        }

        return TimAktif::dengan($tim, function () use ($tim): int {
            $jumlah = Kegiatan::withTrashed()->count();

            if ($jumlah === 0) {
                $this->components->info("Tim {$tim->slug} tidak punya proyek.");

                return self::SUCCESS;
            }

            $this->components->twoColumnDetail("Proyek tim {$tim->slug} ({$tim->nama})", (string) $jumlah);

            if (! $this->confirmToProceed('Seluruh proyek tim ini akan dihapus permanen.')) {
                return self::FAILURE;
            }

            // Berkas dihapus lebih dulu, selagi jalurnya masih terbaca.
            $berkas = Lampiran::withTrashed()->pluck('path')->filter()->all();
            Storage::disk('lampiran')->delete($berkas);

            DB::transaction(function () use ($tim): void {
                Kegiatan::withTrashed()->get()->each->forceDelete();

                if ($this->option('aktivitas')) {
                    ActivityLog::query()->where('team_id', $tim->id)->delete();
                }
            });

            $this->components->info(sprintf('%d proyek dan %d berkas lampiran dihapus.', $jumlah, count($berkas)));

            return self::SUCCESS;
        });
    }
}
