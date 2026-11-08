<?php

declare(strict_types=1);

namespace App\Services\Saas;

use App\Enums\JenisMaster;
use App\Models\Analisa;
use App\Models\MasterData;
use App\Models\Material;
use App\Models\Team;
use App\Support\Tenancy\TimAktif;

/**
 * Isi awal untuk tim baru: daftar acuan, katalog harga, dan template
 * analisa (AHSP) yang umum dipakai.
 *
 * Koefisien diambil dari analisa SNI/AHSP yang lazim; harga hanya
 * perkiraan dan WAJIB disesuaikan tim dengan harga daerahnya masing-masing.
 * Karena itu katalog ini disalin per tim, bukan dibagi bersama: tim di
 * Jayapura dan tim di Bandung tidak boleh saling mengubah harga.
 */
final class KatalogAwal
{
    /** kode => [nama, kategori, satuan, harga] */
    private const MATERIAL = [
        // Upah (orang-hari)
        'U-PKJ' => ['Pekerja', 'upah', 'OH', 120000],
        'U-TKB' => ['Tukang batu', 'upah', 'OH', 150000],
        'U-TKK' => ['Tukang kayu', 'upah', 'OH', 150000],
        'U-TKS' => ['Tukang besi', 'upah', 'OH', 150000],
        'U-TKC' => ['Tukang cat', 'upah', 'OH', 150000],
        'U-TKL' => ['Tukang listrik', 'upah', 'OH', 160000],
        'U-KTK' => ['Kepala tukang', 'upah', 'OH', 170000],
        'U-MDR' => ['Mandor', 'upah', 'OH', 180000],

        // Material
        'M-SMN' => ['Semen PC 50 kg', 'material', 'zak', 70000],
        'M-PSP' => ['Pasir pasang', 'material', 'm3', 280000],
        'M-PSB' => ['Pasir beton', 'material', 'm3', 320000],
        'M-SPL' => ['Kerikil / split 1-2', 'material', 'm3', 330000],
        'M-BTK' => ['Batu kali', 'material', 'm3', 300000],
        'M-BTM' => ['Bata merah', 'material', 'bh', 900],
        'M-BSI' => ['Besi beton', 'material', 'kg', 14000],
        'M-KWT' => ['Kawat bendrat', 'material', 'kg', 25000],
        'M-MPK' => ['Multiplek 9 mm (bekisting)', 'material', 'lbr', 125000],
        'M-PKU' => ['Paku campur', 'material', 'kg', 22000],
        'M-KRM' => ['Keramik 40×40 (dus ±0,96 m²)', 'material', 'dus', 70000],
        'M-NAT' => ['Semen warna / nat', 'material', 'kg', 15000],
        'M-PLM' => ['Plamir tembok', 'material', 'kg', 25000],
        'M-CTD' => ['Cat dasar', 'material', 'kg', 28000],
        'M-CTT' => ['Cat tembok', 'material', 'kg', 30000],
        'M-GYP' => ['Gypsum board 9 mm', 'material', 'lbr', 75000],
        'M-HLW' => ['Rangka hollow 4×4', 'material', 'btg', 35000],
        'M-BJR' => ['Rangka atap baja ringan (terpasang)', 'material', 'm2', 135000],
        'M-GTM' => ['Genteng metal pasir', 'material', 'lbr', 45000],
        'M-PTU' => ['Pintu panel kayu + kusen', 'material', 'unit', 1850000],
        'M-ENG' => ['Engsel pintu', 'material', 'bh', 25000],
        'M-KNC' => ['Kunci pintu (set)', 'material', 'set', 175000],
        'M-JDA' => ['Jendela aluminium + kaca', 'material', 'unit', 1200000],
        'M-NYM' => ['Kabel NYM 2×1,5 mm', 'material', 'm', 6500],
        'M-CND' => ['Pipa conduit', 'material', 'btg', 12000],
        'M-SKL' => ['Saklar', 'material', 'bh', 25000],
        'M-FTG' => ['Fitting + lampu LED', 'material', 'bh', 50000],
    ];

    /** [kode, nama, kategori, satuan, [kode material => koefisien]] */
    private const ANALISA = [
        ['A-GAL', 'Galian tanah biasa sedalam 1 m', 'tanah', 'm3', [
            'U-PKJ' => 0.75, 'U-MDR' => 0.025,
        ]],
        ['A-PBK', 'Pondasi batu kali 1PC : 4PP', 'pondasi', 'm3', [
            'M-BTK' => 1.2, 'M-SMN' => 3.26, 'M-PSP' => 0.52,
            'U-PKJ' => 1.5, 'U-TKB' => 0.75, 'U-KTK' => 0.075, 'U-MDR' => 0.075,
        ]],
        ['A-BTR', 'Beton bertulang K-225 (kolom/balok/sloof) termasuk besi & bekisting', 'struktur', 'm3', [
            'M-SMN' => 7.42, 'M-PSB' => 0.5, 'M-SPL' => 0.78, 'M-BSI' => 150, 'M-KWT' => 2.25,
            'M-MPK' => 2.8, 'M-PKU' => 1.5,
            'U-PKJ' => 5.3, 'U-TKB' => 1.0, 'U-TKS' => 1.05, 'U-TKK' => 1.0, 'U-KTK' => 0.3, 'U-MDR' => 0.27,
        ]],
        ['A-DBM', 'Pasangan dinding bata merah ½ bata 1PC : 4PP', 'dinding', 'm2', [
            'M-BTM' => 70, 'M-SMN' => 0.23, 'M-PSP' => 0.043,
            'U-PKJ' => 0.3, 'U-TKB' => 0.1, 'U-KTK' => 0.01, 'U-MDR' => 0.015,
        ]],
        ['A-PLS', 'Plesteran + acian dinding 1PC : 4PP tebal 15 mm', 'dinding', 'm2', [
            'M-SMN' => 0.2, 'M-PSP' => 0.024,
            'U-PKJ' => 0.5, 'U-TKB' => 0.25, 'U-KTK' => 0.025, 'U-MDR' => 0.025,
        ]],
        ['A-KRM', 'Pasang lantai keramik 40×40', 'lantai', 'm2', [
            'M-KRM' => 1.1, 'M-SMN' => 0.21, 'M-PSP' => 0.045, 'M-NAT' => 1.62,
            'U-PKJ' => 0.7, 'U-TKB' => 0.35, 'U-KTK' => 0.035, 'U-MDR' => 0.035,
        ]],
        ['A-PTU', 'Pasang pintu panel kayu lengkap (kusen, engsel, kunci)', 'kusen', 'unit', [
            'M-PTU' => 1, 'M-ENG' => 3, 'M-KNC' => 1,
            'U-PKJ' => 0.5, 'U-TKK' => 1.0,
        ]],
        ['A-JDA', 'Pasang jendela aluminium + kaca', 'kusen', 'unit', [
            'M-JDA' => 1,
            'U-PKJ' => 0.25, 'U-TKK' => 0.5,
        ]],
        ['A-ATP', 'Atap rangka baja ringan + genteng metal', 'atap', 'm2', [
            'M-BJR' => 1, 'M-GTM' => 1.75,
            'U-PKJ' => 0.1, 'U-TKK' => 0.1,
        ]],
        ['A-PLF', 'Plafon gypsum 9 mm rangka hollow', 'plafon', 'm2', [
            'M-GYP' => 0.364, 'M-HLW' => 1.1,
            'U-PKJ' => 0.1, 'U-TKK' => 0.15, 'U-MDR' => 0.005,
        ]],
        ['A-CAT', 'Pengecatan dinding baru (plamir, dasar, 2× penutup)', 'finishing', 'm2', [
            'M-PLM' => 0.1, 'M-CTD' => 0.1, 'M-CTT' => 0.26,
            'U-PKJ' => 0.02, 'U-TKC' => 0.063, 'U-MDR' => 0.0025,
        ]],
        ['A-LST', 'Instalasi titik lampu lengkap', 'listrik', 'titik', [
            'M-NYM' => 10, 'M-CND' => 3, 'M-SKL' => 1, 'M-FTG' => 1,
            'U-PKJ' => 0.1, 'U-TKL' => 0.25,
        ]],
    ];

    public function isi(Team $tim): void
    {
        TimAktif::dengan($tim, function () use ($tim): void {
            $this->masterData($tim);
            $material = $this->material($tim);
            $this->analisa($tim, $material);
        });
    }

    private function masterData(Team $tim): void
    {
        foreach (JenisMaster::cases() as $jenis) {
            foreach ($jenis->contoh() as $urutan => $nama) {
                MasterData::query()->firstOrCreate(
                    ['team_id' => $tim->id, 'jenis' => $jenis->value, 'nama' => $nama],
                    ['urutan' => $urutan, 'aktif' => true],
                );
            }
        }
    }

    /** @return array<string, int> kode => id */
    private function material(Team $tim): array
    {
        $id = [];

        foreach (self::MATERIAL as $kode => [$nama, $kategori, $satuan, $harga]) {
            $id[$kode] = Material::query()->firstOrCreate(
                ['team_id' => $tim->id, 'kode' => $kode],
                ['nama' => $nama, 'kategori' => $kategori, 'satuan' => $satuan, 'harga' => $harga, 'aktif' => true],
            )->id;
        }

        return $id;
    }

    /** @param array<string, int> $material */
    private function analisa(Team $tim, array $material): void
    {
        foreach (self::ANALISA as [$kode, $nama, $kategori, $satuan, $resep]) {
            $analisa = Analisa::query()->firstOrCreate(
                ['team_id' => $tim->id, 'kode' => $kode],
                ['nama' => $nama, 'kategori' => $kategori, 'satuan' => $satuan],
            );

            if ($analisa->items()->exists()) {
                continue;
            }

            $urutan = 0;

            foreach ($resep as $kodeMaterial => $koefisien) {
                $analisa->items()->create([
                    'material_id' => $material[$kodeMaterial],
                    'koefisien' => $koefisien,
                    'urutan' => $urutan++,
                ]);
            }
        }
    }
}
