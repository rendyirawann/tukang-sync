<?php

namespace Tests\Feature\Admin;

use App\Enums\StatusPembayaran;
use App\Enums\StatusTim;
use App\Models\Plan;
use App\Models\Team;
use App\Models\TeamPayment;
use App\Models\User;
use App\Services\Saas\PenyediaTim;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\SaasSeeder;
use Database\Seeders\SuperAdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SaasAdminTest extends TestCase
{
    use RefreshDatabase;

    private const PNG = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==';

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
        $this->seed([RolePermissionSeeder::class, SuperAdminSeeder::class, SaasSeeder::class]);
        $this->admin = User::whereNull('team_id')->firstOrFail();
    }

    private function timPending(string $email = 'pending@x.test'): TeamPayment
    {
        return app(PenyediaTim::class)->daftar([
            'nama_tim' => 'Tim Uji', 'nama' => 'Pemilik', 'email' => $email,
            'no_wa' => '081234', 'password' => 'rahasia123', 'peran' => 'owner',
        ], Plan::where('kode', 'bulanan')->first());
    }

    public function test_halaman_index_terbuka(): void
    {
        $bayar = $this->timPending();
        $this->actingAs($this->admin);

        foreach (['saas.tim.index', 'saas.tim.create', 'saas.bayar.index', 'saas.paket.index', 'saas.rekening.index', 'saas.kontak.index', 'dashboard'] as $rute) {
            $this->get(route($rute))->assertOk();
        }

        $this->get(route('saas.tim.show', $bayar->team->slug))->assertOk()->assertSee($bayar->team->slug);
        $this->get(route('saas.tim.edit', $bayar->team->slug))->assertOk();
        $this->get(route('saas.bayar.show', $bayar))->assertOk()->assertSee($bayar->kode);
    }

    public function test_superadmin_mendaftarkan_tim_langsung_aktif(): void
    {
        $this->actingAs($this->admin)->post(route('saas.tim.store'), [
            'nama_tim' => 'CV Maju', 'nama' => 'Andi', 'email' => 'andi@x.test', 'no_wa' => '0812',
            'password' => 'rahasia123', 'peran' => 'kontraktor',
            'plan_id' => Plan::where('kode', 'lifetime')->value('id'),
            'langsung_aktif' => 1, 'bukti_data' => self::PNG,
        ])->assertRedirect();

        $tim = Team::where('nama', 'CV Maju')->firstOrFail();
        $this->assertSame(StatusTim::Aktif, $tim->status);
        $this->assertTrue($tim->lifetime);
        $this->assertTrue(User::where('email', 'andi@x.test')->value('is_active'));
        $this->assertNotNull($tim->payments()->first()->bukti_path);
    }

    public function test_terima_pembayaran_dengan_bukti_tempel(): void
    {
        $bayar = $this->timPending();

        $this->actingAs($this->admin)
            ->post(route('saas.bayar.terima', $bayar), ['bukti_data' => self::PNG])
            ->assertRedirect(route('saas.bayar.show', $bayar));

        $bayar->refresh();
        $this->assertSame(StatusPembayaran::Diterima, $bayar->status);
        $this->assertSame('image/png', $bayar->bukti_mime);
        $this->assertTrue($bayar->team->langgananBerlaku());
        $this->assertTrue(User::where('email', 'pending@x.test')->value('is_active'));

        $this->get(route('saas.bayar.bukti', $bayar))->assertOk();
    }

    public function test_anggota_tim_tidak_bisa_membuka_admin(): void
    {
        $bayar = $this->timPending();
        app(PenyediaTim::class)->konfirmasi($bayar, $this->admin);
        $owner = User::where('email', 'pending@x.test')->first();

        $this->actingAs($owner)->get(route('saas.tim.index'))
            ->assertRedirect(route('tim.dashboard', $bayar->team->slug));
    }

    public function test_kuota_penuh_menolak_akun_kelima(): void
    {
        $tim = $this->timPending()->team;
        $this->actingAs($this->admin);

        for ($i = 2; $i <= 4; $i++) {
            $this->post(route('saas.tim.anggota.store', $tim->slug), [
                'name' => "A$i", 'email' => "a$i@x.test", 'peran' => 'anggota', 'password' => 'rahasia123',
            ])->assertSessionHasNoErrors();
        }

        $this->post(route('saas.tim.anggota.store', $tim->slug), [
            'name' => 'A5', 'email' => 'a5@x.test', 'peran' => 'anggota', 'password' => 'rahasia123',
        ])->assertSessionHasErrors('email');

        $this->assertSame(4, $tim->users()->count());
    }
}
