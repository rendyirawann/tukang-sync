<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\Analisa;
use App\Models\Bangunan;
use App\Models\Kegiatan;
use App\Models\Material;
use App\Models\Pekerjaan;
use App\Models\Team;
use App\Models\User;
use App\Services\Saas\KatalogAwal;
use App\Support\Tenancy\TimAktif;
use PHPUnit\Framework\Attributes\Test;

/** Endpoint RAB sesuai docs/API-RAB.md. */
class RabTest extends ApiTestCase
{
    private User $owner;

    private Kegiatan $kegiatan;

    protected function setUp(): void
    {
        parent::setUp();

        app(KatalogAwal::class)->isi($this->tim);
        $this->owner = $this->owner();
        $this->kegiatan = Kegiatan::factory()->create();
    }

    /** @return array{0:int,1:int,2:int} bangunan, lantai, ruangan */
    private function struktur(): array
    {
        $b = $this->actingAs($this->owner)->postJson("/api/kegiatan/{$this->kegiatan->id}/bangunan", [
            'nama' => 'Rumah Utama', 'jenis' => 'rumah', 'luas_bangunan' => 90,
        ])->assertCreated()->assertJsonPath('data.jenis_label', 'Rumah Tinggal')->json('data.id');

        $l = $this->actingAs($this->owner)->postJson("/api/bangunan/{$b}/lantai", ['nama' => 'Lantai 1', 'nomor' => 1])
            ->assertCreated()->json('data.id');

        $r = $this->actingAs($this->owner)->postJson("/api/lantai/{$l}/ruangan", [
            'nama' => 'Dapur', 'panjang' => 3, 'lebar' => 3, 'tinggi' => 3.5,
        ])->assertCreated()
            ->assertJsonPath('data.luas_lantai', 9)
            ->assertJsonPath('data.luas_dinding', 42)
            ->json('data.id');

        return [$b, $l, $r];
    }

    #[Test]
    public function referensi_rab_lengkap(): void
    {
        $this->actingAs($this->owner)->getJson('/api/referensi/rab')
            ->assertOk()
            ->assertJsonStructure(['data' => ['kategori_pekerjaan', 'komponen' => [['value', 'label', 'satuan', 'kategori']], 'satuan' => [['value', 'label', 'simbol', 'dimensi', 'otomatis']], 'jenis_bangunan', 'kategori_material', 'cuaca']]);
    }

    #[Test]
    public function pekerjaan_dengan_analisa_menghitung_volume_kebutuhan_dan_total(): void
    {
        [$b, $l, $r] = $this->struktur();
        $analisa = Analisa::where('kode', 'A-DBM')->firstOrFail();

        $res = $this->actingAs($this->owner)->postJson("/api/kegiatan/{$this->kegiatan->id}/pekerjaan", [
            'kategori' => 'dinding', 'komponen' => 'dinding', 'nama' => 'Dinding dapur',
            'ruangan_id' => $r, 'jumlah' => 1, 'panjang' => 12, 'tinggi' => 3.5, 'satuan' => 'm2',
            'analisa_id' => $analisa->id,
        ])->assertCreated();

        $res->assertJsonPath('data.volume', 42)
            ->assertJsonPath('data.lantai_id', $l)       // diisi otomatis dari ruangan
            ->assertJsonPath('data.bangunan_id', $b)
            ->assertJsonPath('data.lokasi', 'Rumah Utama › Lantai 1 › Dapur')
            ->assertJsonPath('data.analisa_nama', $analisa->nama);

        $this->assertGreaterThan(0, $res->json('data.total'));
        $bata = collect($res->json('data.kebutuhan'))->firstWhere('nama', 'Bata merah');
        $this->assertEquals(70 * 42, $bata['qty']);
        $this->assertSame($res->json('data.total'), collect($res->json('data.kebutuhan'))->sum('subtotal'));

        $this->actingAs($this->owner)->getJson("/api/kegiatan/{$this->kegiatan->id}/rab")
            ->assertOk()
            ->assertJsonPath('data.ringkasan.total_rab', $res->json('data.total'))
            ->assertJsonPath('data.bangunan.0.total', $res->json('data.total'))
            ->assertJsonPath('data.bangunan.0.lantai.0.ruangan.0.total', $res->json('data.total'));

        $rekap = $this->actingAs($this->owner)->getJson("/api/kegiatan/{$this->kegiatan->id}/rekap-material")->assertOk()->json('data');
        $this->assertEquals(2940, collect($rekap)->firstWhere('nama', 'Bata merah')['qty']);
    }

    #[Test]
    public function pekerjaan_borongan_tanpa_analisa_volume_kali_harga(): void
    {
        $id = $this->actingAs($this->owner)->postJson("/api/kegiatan/{$this->kegiatan->id}/pekerjaan", [
            'kategori' => 'kusen', 'komponen' => 'pintu', 'nama' => 'Pintu kamar', 'jumlah' => 3, 'satuan' => 'unit', 'harga_satuan' => 1_500_000,
        ])->assertCreated()->assertJsonPath('data.total', 4_500_000)->json('data.id');

        $this->actingAs($this->owner)->putJson("/api/pekerjaan/{$id}", ['jumlah' => 4])
            ->assertOk()->assertJsonPath('data.total', 6_000_000);
    }

    #[Test]
    public function kebutuhan_bisa_ditambah_diubah_dan_dihapus(): void
    {
        $semen = Material::where('kode', 'M-SMN')->firstOrFail();
        $id = $this->actingAs($this->owner)->postJson("/api/kegiatan/{$this->kegiatan->id}/pekerjaan", [
            'kategori' => 'lantai', 'nama' => 'Rabat beton', 'jumlah' => 1, 'panjang' => 10, 'lebar' => 2, 'satuan' => 'm2',
        ])->assertCreated()->json('data.id');

        $data = $this->actingAs($this->owner)->postJson("/api/pekerjaan/{$id}/kebutuhan", ['material_id' => $semen->id, 'koefisien' => 0.5])
            ->assertCreated()->json('data');
        $this->assertEquals(10, $data['kebutuhan'][0]['qty']);
        $this->assertSame((int) round(10 * $semen->harga), $data['total']);

        $kid = $data['kebutuhan'][0]['id'];
        $this->actingAs($this->owner)->putJson("/api/kebutuhan/{$kid}", ['qty' => 4])
            ->assertOk()->assertJsonPath('data.kebutuhan.0.qty_manual', true)->assertJsonPath('data.kebutuhan.0.qty', 4);

        $this->actingAs($this->owner)->deleteJson("/api/kebutuhan/{$kid}")
            ->assertOk()->assertJsonCount(0, 'data.kebutuhan');
    }

    #[Test]
    public function lokasi_yang_tidak_konsisten_ditolak(): void
    {
        [, , $r] = $this->struktur();
        $proyekLain = Kegiatan::factory()->create();
        $bLain = Bangunan::create(['kegiatan_id' => $proyekLain->id, 'nama' => 'Gudang']);

        $this->actingAs($this->owner)->postJson("/api/kegiatan/{$this->kegiatan->id}/pekerjaan", [
            'kategori' => 'lantai', 'nama' => 'X', 'jumlah' => 1, 'satuan' => 'unit', 'bangunan_id' => $bLain->id,
        ])->assertStatus(422)->assertJsonValidationErrorFor('bangunan_id');

        $this->actingAs($this->owner)->postJson("/api/kegiatan/{$this->kegiatan->id}/pekerjaan", [
            'kategori' => 'lantai', 'nama' => 'X', 'jumlah' => 1, 'satuan' => 'unit', 'ruangan_id' => $r, 'bangunan_id' => $bLain->id,
        ])->assertStatus(422);
    }

    #[Test]
    public function mandor_boleh_isi_progres_dan_log_tetapi_tidak_menyusun_rab(): void
    {
        $mandor = $this->anggota('mandor');
        $p = $this->actingAs($this->owner)->postJson("/api/kegiatan/{$this->kegiatan->id}/pekerjaan", [
            'kategori' => 'atap', 'nama' => 'Atap', 'jumlah' => 1, 'satuan' => 'ls', 'harga_satuan' => 10_000_000,
        ])->json('data.id');

        $this->actingAs($mandor)->patchJson("/api/pekerjaan/{$p}/progres", ['progres' => 40])
            ->assertOk()->assertJsonPath('data.progres', 40)->assertJsonPath('data.nilai_progres', 4_000_000);

        $this->actingAs($mandor)->postJson("/api/kegiatan/{$this->kegiatan->id}/pekerjaan", [
            'kategori' => 'atap', 'nama' => 'Coba', 'jumlah' => 1, 'satuan' => 'ls',
        ])->assertStatus(403);
        $this->actingAs($mandor)->postJson("/api/kegiatan/{$this->kegiatan->id}/bangunan", ['nama' => 'X'])->assertStatus(403);
        $this->actingAs($mandor)->postJson('/api/material', ['nama' => 'X', 'kategori' => 'material', 'satuan' => 'bh', 'harga' => 1])->assertStatus(403);

        $log = $this->actingAs($mandor)->postJson("/api/kegiatan/{$this->kegiatan->id}/log-harian", [
            'tanggal' => now()->toDateString(), 'cuaca' => 'cerah', 'jumlah_pekerja' => 6, 'uraian' => 'Pasang rangka atap',
        ])->assertCreated()->assertJsonPath('data.dibuat_oleh.id', $mandor->id)->assertJsonPath('data.cuaca_label', 'Cerah')->json('data.id');

        $mandor2 = $this->anggota('mandor');
        $this->actingAs($mandor2)->putJson("/api/log-harian/{$log}", ['uraian' => 'Ubah'])->assertStatus(403);
        $this->actingAs($this->owner)->putJson("/api/log-harian/{$log}", ['uraian' => 'Ubah'])->assertOk();

        $this->actingAs($this->owner)->getJson("/api/kegiatan/{$this->kegiatan->id}/log-harian")
            ->assertOk()->assertJsonPath('meta.total', 1);
    }

    #[Test]
    public function katalog_dan_analisa_crud(): void
    {
        $m = $this->actingAs($this->owner)->postJson('/api/material', [
            'nama' => 'Bata ringan', 'kategori' => 'material', 'satuan' => 'bh', 'harga' => 9500,
        ])->assertCreated()->assertJsonPath('data.harga', 9500)->json('data.id');

        $a = $this->actingAs($this->owner)->postJson('/api/analisa', [
            'nama' => 'Dinding bata ringan', 'kategori' => 'dinding', 'satuan' => 'm2',
            'items' => [['material_id' => $m, 'koefisien' => 8.5]],
        ])->assertCreated()->assertJsonPath('data.harga_satuan', 80750)->json('data.id');

        $this->actingAs($this->owner)->putJson("/api/material/{$m}", ['harga' => 10000])->assertOk();
        $this->actingAs($this->owner)->getJson("/api/analisa/{$a}")
            ->assertOk()->assertJsonPath('data.harga_satuan', 85000)->assertJsonPath('data.items.0.nama', 'Bata ringan');

        $daftar = $this->actingAs($this->owner)->getJson('/api/analisa?kategori=dinding')->assertOk()->json('data');
        $this->assertArrayNotHasKey('items', $daftar[0]);

        $this->actingAs($this->owner)->getJson('/api/material?kategori=upah')->assertOk()
            ->assertJsonMissing(['nama' => 'Bata ringan']);

        $this->actingAs($this->owner)->deleteJson("/api/material/{$m}")->assertOk();
        $this->actingAs($this->owner)->deleteJson("/api/analisa/{$a}")->assertOk();
    }

    #[Test]
    public function rab_tim_lain_tidak_bisa_dibaca_atau_diubah(): void
    {
        $lain = Team::factory()->create();
        [$pekerjaan, $bangunan, $material, $analisa, $kegiatan] = TimAktif::dengan($lain, function () use ($lain) {
            app(KatalogAwal::class)->isi($lain);
            $k = Kegiatan::factory()->create();
            $b = Bangunan::create(['kegiatan_id' => $k->id, 'nama' => 'Rahasia']);
            $p = Pekerjaan::create(['kegiatan_id' => $k->id, 'kategori' => 'lainnya', 'nama' => 'Rahasia', 'jumlah' => 1, 'satuan' => 'ls']);

            return [$p, $b, Material::first(), Analisa::first(), $k];
        });
        TimAktif::set($this->tim);

        $this->actingAs($this->owner)->getJson("/api/pekerjaan/{$pekerjaan->id}")->assertNotFound();
        $this->actingAs($this->owner)->patchJson("/api/pekerjaan/{$pekerjaan->id}/progres", ['progres' => 100])->assertNotFound();
        $this->actingAs($this->owner)->putJson("/api/bangunan/{$bangunan->id}", ['nama' => 'X'])->assertNotFound();
        $this->actingAs($this->owner)->putJson("/api/material/{$material->id}", ['harga' => 1])->assertNotFound();
        $this->actingAs($this->owner)->getJson("/api/analisa/{$analisa->id}")->assertNotFound();
        $this->actingAs($this->owner)->getJson("/api/kegiatan/{$kegiatan->id}/rab")->assertNotFound();

        // Memakai material/analisa tim lain lewat body juga ditolak.
        $p = $this->actingAs($this->owner)->postJson("/api/kegiatan/{$this->kegiatan->id}/pekerjaan", [
            'kategori' => 'lainnya', 'nama' => 'Saya', 'jumlah' => 1, 'satuan' => 'ls', 'analisa_id' => $analisa->id,
        ])->assertStatus(422);

        $this->actingAs($this->owner)->getJson('/api/material')->assertOk()
            ->assertJsonCount(Material::count(), 'data');

        $this->assertSame(0, Pekerjaan::withoutGlobalScopes()->whereKey($pekerjaan->id)->value('progres'));
    }

    #[Test]
    public function menghapus_bangunan_ikut_menghapus_isinya(): void
    {
        [$b, , $r] = $this->struktur();
        $this->actingAs($this->owner)->postJson("/api/kegiatan/{$this->kegiatan->id}/pekerjaan", [
            'kategori' => 'lantai', 'nama' => 'Keramik', 'jumlah' => 1, 'satuan' => 'ls', 'ruangan_id' => $r,
        ])->assertCreated();

        $this->actingAs($this->owner)->deleteJson("/api/bangunan/{$b}")->assertOk();

        $this->actingAs($this->owner)->getJson("/api/kegiatan/{$this->kegiatan->id}/pekerjaan")
            ->assertOk()->assertJsonCount(0, 'data');
        $this->actingAs($this->owner)->getJson("/api/kegiatan/{$this->kegiatan->id}/rab")
            ->assertOk()->assertJsonCount(0, 'data.bangunan');
    }
}
