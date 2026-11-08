<?php

declare(strict_types=1);

namespace App\Services\Rab;

use App\Enums\KategoriPekerjaan;
use App\Models\Analisa;
use App\Models\BahanBakuItem;
use App\Models\Kegiatan;
use App\Models\Pekerjaan;
use App\Models\PekerjaanMaterial;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Semua hitungan RAB di satu tempat, dipakai panel web dan API.
 */
final class RabCalculator
{
    /**
     * Menghitung ulang volume, kebutuhan, dan total satu pekerjaan, lalu
     * menyimpannya.
     */
    public function hitungUlang(Pekerjaan $p): Pekerjaan
    {
        if (! $p->volume_manual && ($satuan = $p->satuanEnum()) !== null && $satuan->otomatis()) {
            $p->volume = round($satuan->hitung((float) $p->jumlah, $p->panjang, $p->lebar, $p->tinggi), 4);
        }

        $kebutuhan = $p->kebutuhan()->get();

        foreach ($kebutuhan as $k) {
            if (! $k->qty_manual && $k->koefisien !== null) {
                $k->qty = round($k->koefisien * $p->volume, 4);
            }

            $k->subtotal = (int) round($k->qty * $k->harga_satuan);
            $k->save();
        }

        if ($kebutuhan->isNotEmpty()) {
            $p->total = (int) $kebutuhan->sum('subtotal');
            $p->harga_satuan = $p->volume > 0 ? round($p->total / $p->volume, 2) : 0;
        } else {
            $p->total = (int) round($p->volume * $p->harga_satuan);
        }

        $p->save();

        return $p;
    }

    /**
     * Mengganti rincian kebutuhan pekerjaan dengan resep analisa. Harga
     * disalin dari katalog SAAT INI, supaya perubahan harga katalog di
     * kemudian hari tidak diam-diam mengubah RAB yang sudah disepakati.
     */
    public function terapkanAnalisa(Pekerjaan $p, Analisa $analisa): Pekerjaan
    {
        return DB::transaction(function () use ($p, $analisa) {
            $analisa->loadMissing('items.material');

            $p->kebutuhan()->delete();
            $p->analisa_id = $analisa->id;

            foreach ($analisa->items as $item) {
                if ($item->material === null) {
                    continue;
                }

                $p->kebutuhan()->create([
                    'material_id' => $item->material_id,
                    'nama' => $item->material->nama,
                    'kategori' => $item->material->kategori,
                    'satuan' => $item->material->satuan,
                    'koefisien' => $item->koefisien,
                    'harga_satuan' => $item->material->harga,
                ]);
            }

            return $this->hitungUlang($p);
        });
    }

    /** Menyegarkan harga kebutuhan dari katalog terkini (aksi sengaja). */
    public function segarkanHarga(Pekerjaan $p): Pekerjaan
    {
        foreach ($p->kebutuhan()->with('material')->get() as $k) {
            if ($k->material) {
                $k->harga_satuan = $k->material->harga;
                $k->save();
            }
        }

        return $this->hitungUlang($p);
    }

    /**
     * Ringkasan RAB satu proyek.
     *
     * @return array<string, mixed>
     */
    public function ringkasan(Kegiatan $kegiatan): array
    {
        $pekerjaan = $kegiatan->pekerjaan()
            ->with(['bangunan:id,nama', 'lantai:id,nama,nomor', 'ruangan:id,nama'])
            ->orderBy('urutan')->orderBy('id')
            ->get();

        $total = (int) $pekerjaan->sum('total');
        $nilaiProgres = (int) $pekerjaan->sum(fn (Pekerjaan $p) => $p->nilaiProgres());

        $perKategori = $pekerjaan
            ->groupBy(fn (Pekerjaan $p) => $p->kategori?->value ?? 'lainnya')
            ->map(fn (Collection $g, string $k) => [
                'kategori' => $k,
                'label' => KategoriPekerjaan::tryFrom($k)?->label() ?? ucfirst($k),
                'jumlah_item' => $g->count(),
                'total' => (int) $g->sum('total'),
                'persen' => $total > 0 ? round($g->sum('total') / $total * 100, 1) : 0,
            ])
            ->sortBy(fn ($r) => array_search($r['kategori'], array_column(KategoriPekerjaan::cases(), 'value'), true))
            ->values();

        $perLokasi = $pekerjaan
            ->groupBy(fn (Pekerjaan $p) => ($p->bangunan?->nama ?? 'Umum').($p->lantai ? ' › '.$p->lantai->nama : ''))
            ->map(fn (Collection $g, string $lokasi) => [
                'lokasi' => $lokasi,
                'jumlah_item' => $g->count(),
                'total' => (int) $g->sum('total'),
                'progres' => $g->sum('total') > 0
                    ? round($g->sum(fn (Pekerjaan $p) => $p->nilaiProgres()) / $g->sum('total') * 100, 1)
                    : 0,
            ])
            ->values();

        return [
            'total_rab' => $total,
            'jumlah_item' => $pekerjaan->count(),
            'progres' => $total > 0 ? round($nilaiProgres / $total * 100, 1) : 0,
            'nilai_progres' => $nilaiProgres,
            'per_kategori' => $perKategori,
            'per_lokasi' => $perLokasi,
            'realisasi_belanja' => (int) BahanBakuItem::where('kegiatan_id', $kegiatan->id)->sum('subtotal'),
            'pagu' => (int) $kegiatan->pagu,
        ];
    }

    /**
     * Rekap kebutuhan material seluruh proyek -- daftar belanja -- beserta
     * realisasi pembelian yang ditautkan ke material yang sama.
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function rekapMaterial(Kegiatan $kegiatan): Collection
    {
        $rencana = PekerjaanMaterial::query()
            ->whereIn('pekerjaan_id', $kegiatan->pekerjaan()->select('id'))
            ->selectRaw('material_id, MAX(nama) AS nama, MAX(kategori) AS kategori, satuan, SUM(qty) AS qty, SUM(subtotal) AS subtotal')
            ->groupBy('material_id', 'satuan')
            ->orderByRaw('MAX(kategori), MAX(nama)')
            ->get();

        $realisasi = BahanBakuItem::query()
            ->where('kegiatan_id', $kegiatan->id)
            ->whereNotNull('material_id')
            ->selectRaw('material_id, SUM(qty) AS qty, SUM(subtotal) AS subtotal')
            ->groupBy('material_id')
            ->get()
            ->keyBy('material_id');

        return $rencana->map(function ($r) use ($realisasi) {
            $real = $r->material_id ? $realisasi->get($r->material_id) : null;
            $qtyReal = (float) ($real->qty ?? 0);
            $biayaReal = (int) ($real->subtotal ?? 0);

            return [
                'material_id' => $r->material_id,
                'nama' => $r->nama,
                'kategori' => $r->kategori,
                'satuan' => $r->satuan,
                'qty' => round((float) $r->qty, 3),
                'subtotal' => (int) $r->subtotal,
                'qty_realisasi' => round($qtyReal, 3),
                'biaya_realisasi' => $biayaReal,
                'selisih' => (int) $r->subtotal - $biayaReal,
            ];
        });
    }
}
