<?php

namespace Tests\Feature\Tim;

use App\Enums\Peran;
use App\Models\Analisa;
use App\Models\Kegiatan;
use App\Models\Pekerjaan;
use App\Models\Plan;
use App\Support\Tenancy\TimAktif;

class PanelTimTest extends TimTestCase
{
    public function test_login_benar_masuk_dasbor(): void
    {
        $this->post($this->url('login'), ['email' => 'owner@demo.test', 'password' => 'password123'])
            ->assertRedirect(route('tim.dashboard', 'DEMO'));
        $this->get($this->url('dashboard'))->assertOk()->assertSee('CV Bangun Jaya');
    }

    public function test_akun_tim_lain_tidak_bisa_login_di_kode_ini(): void
    {
        $lain = $this->timLain();

        $this->post("/{$lain->slug}/login", ['email' => 'owner@demo.test', 'password' => 'password123'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_kode_huruf_kecil_dialihkan_ke_huruf_besar(): void
    {
        $this->get('/demo/login')->assertRedirect('/DEMO/login');
    }

    public function test_semua_halaman_panel_tampil(): void
    {
        $this->actingAs($this->owner());
        $k = Kegiatan::firstOrFail();
        $p = Pekerjaan::firstOrFail();

        foreach (['dashboard', 'proyek', 'proyek/baru', "proyek/{$k->id}", "proyek/{$k->id}?tab=rab", "proyek/{$k->id}?tab=material",
            "proyek/{$k->id}?tab=log", "proyek/{$k->id}?tab=belanja", "proyek/{$k->id}/pekerjaan/{$p->id}", "proyek/{$k->id}/pekerjaan/baru",
            'material', 'analisa', 'analisa/baru', 'anggota', 'langganan', 'akun'] as $path) {
            $this->get($this->url($path))->assertOk();
        }
    }

    public function test_crud_proyek_dan_rab_dengan_analisa(): void
    {
        $this->actingAs($this->owner());

        $this->post($this->url('proyek'), ['nama' => 'Gudang Baru', 'status' => 'draft', 'pagu' => 100000000])->assertRedirect();
        $proyek = Kegiatan::where('nama', 'Gudang Baru')->firstOrFail();
        $this->assertSame($this->demo->id, $proyek->team_id);

        $this->put($this->url("proyek/{$proyek->id}"), ['nama' => 'Gudang Baru 2', 'status' => 'berjalan'])->assertRedirect();
        $this->assertSame('Gudang Baru 2', $proyek->fresh()->nama);

        $this->post($this->url("proyek/{$proyek->id}/bangunan"), ['nama' => 'Gudang', 'jenis' => 'gudang'])->assertRedirect();
        $b = $proyek->bangunan()->firstOrFail();
        $this->post($this->url("proyek/{$proyek->id}/bangunan/{$b->id}/lantai"), ['nama' => 'Lantai 1', 'nomor' => 1])->assertRedirect();
        $l = $b->lantai()->firstOrFail();
        $this->post($this->url("proyek/{$proyek->id}/lantai/{$l->id}/ruangan"), ['nama' => 'Kantor', 'panjang' => 4, 'lebar' => 3])->assertRedirect();
        $r = $l->ruangan()->firstOrFail();

        TimAktif::set($this->demo);
        $analisa = Analisa::where('kode', 'A-DBM')->firstOrFail();
        TimAktif::reset();

        $this->post($this->url("proyek/{$proyek->id}/pekerjaan"), [
            'bangunan_id' => $b->id, 'lantai_id' => $l->id, 'ruangan_id' => $r->id,
            'komponen' => 'dinding', 'kategori' => 'dinding', 'nama' => 'Dinding kantor',
            'jumlah' => 1, 'panjang' => 14, 'tinggi' => 3, 'satuan' => 'm2', 'analisa_id' => $analisa->id,
        ])->assertRedirect();

        $p = Pekerjaan::where('nama', 'Dinding kantor')->firstOrFail();
        $this->assertEqualsWithDelta(42.0, $p->volume, 0.001);
        $this->assertGreaterThan(0, $p->total);
        $this->assertGreaterThan(0, $p->kebutuhan()->count());

        $this->get($this->url("proyek/{$proyek->id}?tab=material"))->assertOk()->assertSee('Bata merah');

        $this->delete($this->url("proyek/{$proyek->id}"))->assertRedirect();
        $this->assertSoftDeleted($proyek);
    }

    public function test_lokasi_dari_proyek_lain_ditolak(): void
    {
        $this->actingAs($this->owner());
        $k1 = Kegiatan::firstOrFail();
        $this->post($this->url('proyek'), ['nama' => 'Proyek B', 'status' => 'draft']);
        $k2 = Kegiatan::where('nama', 'Proyek B')->firstOrFail();
        $b1 = $k1->bangunan()->firstOrFail();

        $this->post($this->url("proyek/{$k2->id}/pekerjaan"), [
            'bangunan_id' => $b1->id, 'komponen' => 'lainnya', 'kategori' => 'lainnya', 'nama' => 'X', 'jumlah' => 1, 'satuan' => 'ls',
        ])->assertSessionHasErrors('bangunan_id');

        $this->put($this->url("proyek/{$k2->id}/bangunan/{$b1->id}"), ['nama' => 'X', 'jenis' => 'rumah'])->assertNotFound();
    }

    public function test_mandor_bisa_isi_progres_tapi_tidak_membuat_pekerjaan(): void
    {
        $this->actingAs($this->mandor());
        $p = Pekerjaan::firstOrFail();

        $this->patch($this->url("proyek/{$p->kegiatan_id}/pekerjaan/{$p->id}/progres"), ['progres' => 55])->assertRedirect();
        $this->assertSame(55, $p->fresh()->progres);

        $this->post($this->url("proyek/{$p->kegiatan_id}/pekerjaan"), [
            'komponen' => 'lainnya', 'kategori' => 'lainnya', 'nama' => 'X', 'jumlah' => 1, 'satuan' => 'ls',
        ])->assertForbidden();

        $this->post($this->url("proyek/{$p->kegiatan_id}/log-harian"), ['tanggal' => now()->toDateString(), 'uraian' => 'Cor kolom'])->assertRedirect();
        $this->assertDatabaseHas('log_harian', ['uraian' => 'Cor kolom', 'team_id' => $this->demo->id]);
    }

    public function test_viewer_tidak_bisa_menulis(): void
    {
        $viewer = $this->anggota($this->demo, Peran::Viewer, 'viewer@demo.test');
        $this->actingAs($viewer);
        $k = Kegiatan::firstOrFail();

        $this->get($this->url("proyek/{$k->id}"))->assertOk();
        $this->from($this->url('proyek'))->post($this->url('proyek'), ['nama' => 'Tidak boleh', 'status' => 'draft'])
            ->assertRedirect($this->url('proyek'))->assertSessionHas('error');
        $this->assertDatabaseMissing('kegiatan', ['nama' => 'Tidak boleh']);
    }

    public function test_masa_langganan_habis_hanya_baca(): void
    {
        $this->demo->update(['lifetime' => false, 'aktif_sampai' => now()->subDay()]);
        $this->actingAs($this->owner());

        $this->get($this->url('dashboard'))->assertOk()->assertSee('hanya baca');
        $this->post($this->url('proyek'), ['nama' => 'Saat habis', 'status' => 'draft'])->assertSessionHas('error');
        $this->assertDatabaseMissing('kegiatan', ['nama' => 'Saat habis']);

        // Langganan & akun tetap bisa diubah.
        $this->get($this->url('langganan'))->assertOk();
        $this->put($this->url('akun/profil'), ['name' => 'Budi Baru'])->assertSessionHas('success');
    }

    public function test_tim_lain_tidak_bisa_membuka_proyek_demo(): void
    {
        $lain = $this->timLain();
        $k = Kegiatan::firstOrFail();
        $this->actingAs($lain->pemilik());

        $this->get("/{$lain->slug}/proyek/{$k->id}")->assertNotFound();
        // Membuka panel DEMO dengan akun tim lain dialihkan ke timnya sendiri.
        $this->get($this->url('dashboard'))->assertRedirect(route('tim.dashboard', $lain->slug));
    }

    public function test_anggota_kuota_dan_langganan_ke_wa(): void
    {
        $this->actingAs($this->owner());

        $this->post($this->url('anggota'), ['name' => 'C', 'email' => 'c@demo.test', 'peran' => 'anggota', 'password' => 'password123'])->assertSessionHas('success');
        $this->post($this->url('anggota'), ['name' => 'D', 'email' => 'd@demo.test', 'peran' => 'viewer', 'password' => 'password123'])->assertSessionHas('success');
        $this->post($this->url('anggota'), ['name' => 'E', 'email' => 'e@demo.test', 'peran' => 'anggota', 'password' => 'password123'])->assertSessionHasErrors('email');

        $plan = Plan::where('kode', 'bulanan')->first();
        $res = $this->post($this->url('langganan'), ['plan_id' => $plan->id]);
        $this->assertStringStartsWith('https://wa.me/', $res->headers->get('Location'));
        $this->assertDatabaseHas('team_payments', ['team_id' => $this->demo->id, 'jenis' => 'perpanjangan', 'status' => 'menunggu']);
    }

    public function test_mandor_tidak_bisa_kelola_anggota_dan_katalog(): void
    {
        $this->actingAs($this->mandor());
        $this->post($this->url('anggota'), ['name' => 'X', 'email' => 'x@demo.test', 'peran' => 'anggota', 'password' => 'password123'])->assertForbidden();
        $this->post($this->url('material'), ['nama' => 'X', 'kategori' => 'material', 'satuan' => 'bh', 'harga' => 1])->assertForbidden();
    }
}
