<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\JenisKomponen;
use App\Enums\Peran;
use App\Enums\StatusKegiatan;
use App\Models\Analisa;
use App\Models\Kegiatan;
use App\Models\Pekerjaan;
use App\Models\Plan;
use App\Models\Team;
use App\Models\User;
use App\Services\Rab\RabCalculator;
use App\Services\Saas\PenyediaTim;
use App\Support\Tenancy\TimAktif;
use Illuminate\Database\Seeder;

/**
 * Tim contoh "DEMO" yang sudah aktif, lengkap dengan proyek rumah 2 lantai
 * dan RAB-nya, supaya panel tim dan aplikasi bisa langsung dicoba.
 *
 *   URL login : /DEMO/login
 *   Owner     : owner@demo.test   / password123
 *   Mandor    : mandor@demo.test  / password123
 */
class DemoTimSeeder extends Seeder
{
    public function run(PenyediaTim $penyedia, RabCalculator $rab): void
    {
        if (Team::withTrashed()->where('slug', 'DEMO')->exists()) {
            return;
        }

        $admin = User::whereNull('team_id')->firstOrFail();

        $bayar = $penyedia->daftar([
            'nama_tim' => 'CV Bangun Jaya (Demo)',
            'kota' => 'Bandung',
            'nama' => 'Budi Santoso',
            'email' => 'owner@demo.test',
            'no_wa' => '081200000001',
            'password' => 'password123',
            'peran' => Peran::Owner->value,
        ], Plan::where('kode', 'lifetime')->firstOrFail());

        $tim = $bayar->team;
        $tim->update(['slug' => 'DEMO']);
        $penyedia->konfirmasi($bayar, $admin, 'Data contoh');

        User::create([
            'team_id' => $tim->id,
            'peran' => Peran::Mandor->value,
            'name' => 'Sinta Mandor',
            'email' => 'mandor@demo.test',
            'password' => 'password123',
            'is_active' => true,
        ]);

        TimAktif::dengan($tim->fresh(), function () use ($tim, $rab): void {
            $owner = $tim->pemilik();

            $proyek = Kegiatan::create([
                'kode' => 'PRJ-001',
                'nama' => 'Rumah Tinggal 2 Lantai - Bpk. Andi',
                'lokasi' => 'Jl. Melati No. 5, Bandung',
                'status' => StatusKegiatan::cases()[1] ?? StatusKegiatan::cases()[0],
                'pagu' => 450000000,
                'tanggal_mulai' => now()->subMonth(),
                'tanggal_selesai' => now()->addMonths(5),
                'created_by' => $owner?->id,
            ]);

            $rumah = $proyek->bangunan()->create(['nama' => 'Rumah Utama', 'jenis' => 'rumah', 'luas_tanah' => 120, 'luas_bangunan' => 140]);
            $lt1 = $rumah->lantai()->create(['nama' => 'Lantai 1', 'nomor' => 1, 'luas' => 80, 'tinggi' => 3.5]);
            $lt2 = $rumah->lantai()->create(['nama' => 'Lantai 2', 'nomor' => 2, 'luas' => 60, 'tinggi' => 3.2]);

            $ruang = [
                'tamu' => $lt1->ruangan()->create(['nama' => 'Ruang Tamu', 'panjang' => 4, 'lebar' => 3.5, 'tinggi' => 3.5]),
                'dapur' => $lt1->ruangan()->create(['nama' => 'Dapur', 'panjang' => 3, 'lebar' => 3, 'tinggi' => 3.5]),
                'kt1' => $lt2->ruangan()->create(['nama' => 'Kamar Tidur 1', 'panjang' => 4, 'lebar' => 3, 'tinggi' => 3.2]),
            ];

            $a = fn (string $kode) => Analisa::where('kode', $kode)->first();

            $baris = [
                // [lantai, ruangan, komponen, nama, jumlah, p, l, t, analisa, progres]
                [null, null, JenisKomponen::Pondasi, 'Pondasi batu kali keliling', 1, 48, 0.6, 0.8, 'A-PBK', 100],
                [$lt1, null, JenisKomponen::Kolom, 'Kolom K1 20×20 Lantai 1', 12, 0.2, 0.2, 3.5, 'A-BTR', 100],
                [$lt1, null, JenisKomponen::Balok, 'Ring balok 15×20 Lantai 1', 1, 40, 0.15, 0.2, 'A-BTR', 60],
                [$lt1, $ruang['tamu'], JenisKomponen::Dinding, 'Dinding bata ruang tamu', 1, 15, null, 3.5, 'A-DBM', 80],
                [$lt1, $ruang['tamu'], JenisKomponen::Lantai, 'Keramik ruang tamu', 1, 4, 3.5, null, 'A-KRM', 0],
                [$lt1, $ruang['tamu'], JenisKomponen::Pintu, 'Pintu utama panel kayu', 2, null, null, null, 'A-PTU', 0],
                [$lt1, $ruang['tamu'], JenisKomponen::Jendela, 'Jendela aluminium ruang tamu', 3, null, null, null, 'A-JDA', 0],
                [$lt1, $ruang['dapur'], JenisKomponen::Lantai, 'Keramik dapur', 1, 3, 3, null, 'A-KRM', 0],
                [$lt1, null, JenisKomponen::TitikListrik, 'Titik lampu Lantai 1', 8, null, null, null, 'A-LST', 0],
                [$lt2, null, JenisKomponen::Kolom, 'Kolom K1 20×20 Lantai 2', 10, 0.2, 0.2, 3.2, 'A-BTR', 20],
                [$lt2, $ruang['kt1'], JenisKomponen::Dinding, 'Dinding bata kamar tidur 1', 1, 14, null, 3.2, 'A-DBM', 0],
                [$lt2, $ruang['kt1'], JenisKomponen::Jendela, 'Jendela kamar tidur 1', 2, null, null, null, 'A-JDA', 0],
                [$lt2, null, JenisKomponen::Atap, 'Atap baja ringan + genteng metal', 1, 10, 8, null, 'A-ATP', 0],
            ];

            foreach ($baris as $i => [$lantai, $ruangan, $komponen, $nama, $jumlah, $p, $l, $t, $kodeAnalisa, $progres]) {
                $analisa = $a($kodeAnalisa);

                $pekerjaan = Pekerjaan::create([
                    'kegiatan_id' => $proyek->id,
                    'bangunan_id' => $rumah->id,
                    'lantai_id' => $lantai?->id,
                    'ruangan_id' => $ruangan?->id,
                    'kategori' => $komponen->kategoriBawaan(),
                    'komponen' => $komponen,
                    'nama' => $nama,
                    'jumlah' => $jumlah,
                    'panjang' => $p,
                    'lebar' => $l,
                    'tinggi' => $t,
                    'satuan' => $analisa?->satuan ?? $komponen->satuanBawaan(),
                    'progres' => $progres,
                    'urutan' => $i,
                    'created_by' => $owner?->id,
                ]);

                $analisa ? $rab->terapkanAnalisa($pekerjaan, $analisa) : $rab->hitungUlang($pekerjaan);
            }
        });

        $this->command?->info('Tim demo   : /DEMO/login  (owner@demo.test / password123)');
    }
}
