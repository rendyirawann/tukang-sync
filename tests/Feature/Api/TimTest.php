<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\BahanBakuItem;
use App\Models\CashFlow;
use App\Models\Kegiatan;
use App\Models\Team;
use App\Models\User;
use App\Support\Tenancy\TimAktif;
use Illuminate\Support\Facades\RateLimiter;
use PHPUnit\Framework\Attributes\Test;

/**
 * Batas tim: login per kode tim, status langganan, viewer, dan isolasi data
 * antartim.
 */
class TimTest extends ApiTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        RateLimiter::clear('login:owner@tim.test|127.0.0.1');
    }

    private function ownerTim(?Team $tim = null): User
    {
        return User::factory()->forTeam($tim ?? $this->tim, 'owner')->create([
            'email' => 'owner@tim.test',
            'password' => 'password123',
        ]);
    }

    private function login(array $data)
    {
        return $this->postJson('/api/auth/login', $data + ['email' => 'owner@tim.test', 'password' => 'password123']);
    }

    /** Membuat tim kedua beserta isinya tanpa mencemari tim aktif test. */
    private function timLain(callable $isi): mixed
    {
        $lain = Team::factory()->create();
        $hasil = TimAktif::dengan($lain, fn () => $isi($lain));
        TimAktif::set($this->tim);

        return $hasil;
    }

    // ------------------------------------------------------------- login

    #[Test]
    public function login_dengan_kode_tim_yang_cocok_berhasil_dan_mengirim_data_tim(): void
    {
        $this->ownerTim();

        $this->login(['kode_tim' => $this->tim->slug])
            ->assertOk()
            ->assertJsonPath('data.user.peran', 'owner')
            ->assertJsonPath('data.user.role', 'superadmin')
            ->assertJsonPath('data.user.tim.kode', $this->tim->slug)
            ->assertJsonPath('data.user.tim.hanya_baca', false);
    }

    #[Test]
    public function kode_tim_huruf_kecil_diterima(): void
    {
        $this->ownerTim();

        $this->login(['kode_tim' => strtolower($this->tim->slug)])->assertOk();
    }

    #[Test]
    public function kode_tim_yang_salah_ditolak_seperti_sandi_salah(): void
    {
        $this->ownerTim();
        $lain = Team::factory()->create();

        $this->login(['kode_tim' => $lain->slug])
            ->assertStatus(401)
            ->assertJsonPath('code', 'INVALID_CREDENTIALS');
    }

    #[Test]
    public function akun_platform_tidak_bisa_login_lewat_api(): void
    {
        TimAktif::reset();
        User::factory()->create(['email' => 'owner@tim.test', 'password' => 'password123']);
        TimAktif::set($this->tim);

        $this->login([])->assertStatus(401);
    }

    #[Test]
    public function tim_yang_belum_dikonfirmasi_ditolak_team_pending(): void
    {
        $this->tim->update(['status' => 'menunggu']);
        $this->ownerTim();

        $this->login([])->assertStatus(403)->assertJsonPath('code', 'TEAM_PENDING');
    }

    #[Test]
    public function tim_ditangguhkan_ditolak_bahkan_dengan_token_lama(): void
    {
        $owner = $this->ownerTim();
        $token = $this->login([])->assertOk()->json('data.access_token');

        $this->tim->update(['status' => 'ditangguhkan']);
        $this->app['auth']->forgetGuards();

        $this->login([])->assertStatus(403)->assertJsonPath('code', 'TEAM_SUSPENDED');
        $this->withToken($token)->getJson('/api/kegiatan')
            ->assertStatus(403)->assertJsonPath('code', 'TEAM_SUSPENDED');
    }

    // ---------------------------------------------------- masa langganan

    #[Test]
    public function langganan_habis_boleh_membaca_tetapi_tidak_menulis(): void
    {
        $this->tim->update(['aktif_sampai' => now()->subDay(), 'lifetime' => false]);
        $owner = $this->ownerTim();

        $this->actingAs($owner)->getJson('/api/kegiatan')->assertOk();

        $this->actingAs($owner)->postJson('/api/kegiatan', ['nama' => 'Baru', 'pagu' => 1_000_000])
            ->assertStatus(402)
            ->assertJsonPath('code', 'SUBSCRIPTION_EXPIRED');

        // Urusan akun sendiri tetap boleh supaya tidak terkunci.
        $this->actingAs($owner)->postJson('/api/auth/logout')->assertOk();
    }

    #[Test]
    public function lifetime_tidak_pernah_habis(): void
    {
        $this->tim->update(['aktif_sampai' => null, 'lifetime' => true]);
        $owner = $this->ownerTim();

        $this->actingAs($owner)->postJson('/api/kegiatan', ['nama' => 'Baru', 'pagu' => 1_000_000])->assertCreated();
    }

    #[Test]
    public function viewer_hanya_bisa_membaca(): void
    {
        $viewer = $this->anggota('viewer');
        $kegiatan = Kegiatan::factory()->create();

        $this->actingAs($viewer)->getJson("/api/kegiatan/{$kegiatan->id}")->assertOk();
        $this->actingAs($viewer)->postJson("/api/kegiatan/{$kegiatan->id}/bahan-baku", [
            'nama' => 'Semen', 'qty' => 1, 'harga_satuan' => 70000,
        ])->assertStatus(403);
    }

    // --------------------------------------------------- isolasi data

    #[Test]
    public function data_tim_lain_tidak_terlihat_dan_tidak_bisa_disentuh(): void
    {
        $owner = $this->ownerTim();
        $milikSendiri = Kegiatan::factory()->create(['nama' => 'Proyek Sendiri']);

        [$kegiatanLain, $kasLain, $bahanLain] = $this->timLain(function () {
            $k = Kegiatan::factory()->create(['nama' => 'Proyek Rahasia']);

            return [
                $k,
                CashFlow::factory()->untuk($k)->create(),
                BahanBakuItem::factory()->untuk($k)->create(),
            ];
        });

        $this->actingAs($owner)->getJson('/api/kegiatan')
            ->assertOk()
            ->assertJsonFragment(['nama' => 'Proyek Sendiri'])
            ->assertJsonMissing(['nama' => 'Proyek Rahasia']);

        $this->actingAs($owner)->getJson("/api/kegiatan/{$kegiatanLain->id}")->assertNotFound();
        $this->actingAs($owner)->putJson("/api/kegiatan/{$kegiatanLain->id}", ['nama' => 'Dibajak'])->assertNotFound();
        $this->actingAs($owner)->deleteJson("/api/kegiatan/{$kegiatanLain->id}")->assertNotFound();
        $this->actingAs($owner)->getJson("/api/cash-flows/{$kasLain->id}")->assertNotFound();
        $this->actingAs($owner)->deleteJson("/api/cash-flows/{$kasLain->id}")->assertNotFound();
        $this->actingAs($owner)->putJson("/api/bahan-baku/{$bahanLain->id}", ['nama' => 'X', 'qty' => 1, 'harga_satuan' => 1])->assertNotFound();
        $this->actingAs($owner)->getJson('/api/cash-flows')->assertOk()->assertJsonMissing(['id' => $kasLain->id, 'kegiatan_id' => $kegiatanLain->id]);

        $this->assertSame('Proyek Rahasia', Kegiatan::withoutGlobalScopes()->find($kegiatanLain->id)->nama);
        $this->assertNotNull($milikSendiri->fresh());
    }

    #[Test]
    public function kode_kegiatan_unik_per_tim_bukan_global(): void
    {
        $owner = $this->ownerTim();
        $this->timLain(fn () => Kegiatan::factory()->create(['kode' => 'PRJ-001']));

        $this->actingAs($owner)->postJson('/api/kegiatan', ['nama' => 'A', 'pagu' => 1_000_000, 'kode' => 'PRJ-001'])->assertCreated();
        $this->actingAs($owner)->postJson('/api/kegiatan', ['nama' => 'B', 'pagu' => 1_000_000, 'kode' => 'PRJ-001'])->assertStatus(422);
    }

    #[Test]
    public function data_baru_lewat_api_otomatis_milik_tim_pengirim(): void
    {
        $owner = $this->ownerTim();

        $id = $this->actingAs($owner)->postJson('/api/kegiatan', ['nama' => 'Milik Siapa', 'pagu' => 1_000_000])
            ->assertCreated()->json('data.id');

        $this->assertSame($this->tim->id, Kegiatan::withoutGlobalScopes()->find($id)->team_id);
    }
}
