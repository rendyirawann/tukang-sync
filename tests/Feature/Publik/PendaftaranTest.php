<?php

namespace Tests\Feature\Publik;

use App\Enums\StatusTim;
use App\Models\Plan;
use App\Models\Team;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\SaasSeeder;
use Database\Seeders\SuperAdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PendaftaranTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([RolePermissionSeeder::class, SuperAdminSeeder::class, SaasSeeder::class]);
    }

    public function test_beranda_dan_form_daftar_tampil(): void
    {
        $this->get('/')->assertOk()->assertSee('Lifetime')->assertSee('Rp 49.000');
        $this->get('/daftar?paket=lifetime')->assertOk()->assertSee('Daftarkan tim Anda');
    }

    public function test_daftar_membuat_tim_menunggu_dan_halaman_status_berisi_link_wa(): void
    {
        $plan = Plan::where('kode', 'bulanan')->first();

        $res = $this->post('/daftar', [
            'nama_tim' => 'CV Uji', 'nama' => 'Andi', 'email' => 'andi@uji.test', 'no_wa' => '081234567890',
            'password' => 'rahasia123', 'password_confirmation' => 'rahasia123', 'peran' => 'kontraktor', 'plan_id' => $plan->id,
        ]);

        $tim = Team::where('nama', 'CV Uji')->firstOrFail();
        $res->assertRedirect(route('tim.status', [$tim->slug, 'baru' => 1]));
        $this->assertSame(StatusTim::Menunggu, $tim->status);
        $this->assertFalse(User::where('email', 'andi@uji.test')->first()->is_active);

        $this->get("/{$tim->slug}/status?baru=1")->assertOk()
            ->assertSee($tim->slug)->assertSee('wa.me/6281234567890', false)->assertSee('Rp 49.000');
    }

    public function test_honeypot_tidak_membuat_tim(): void
    {
        $this->post('/daftar', ['situs_web' => 'spam', 'nama_tim' => 'Bot'])->assertRedirect(route('beranda'));
        $this->assertSame(0, Team::count());
    }

    public function test_login_tim_menunggu_dialihkan_ke_status(): void
    {
        $plan = Plan::first();
        $this->post('/daftar', [
            'nama_tim' => 'CV Tunggu', 'nama' => 'B', 'email' => 'b@uji.test', 'no_wa' => '081234567890',
            'password' => 'rahasia123', 'password_confirmation' => 'rahasia123', 'peran' => 'owner', 'plan_id' => $plan->id,
        ]);
        $tim = Team::firstOrFail();

        $this->post("/{$tim->slug}/login", ['email' => 'b@uji.test', 'password' => 'rahasia123'])
            ->assertRedirect(route('tim.status', $tim->slug));
        $this->assertGuest();
    }
}
