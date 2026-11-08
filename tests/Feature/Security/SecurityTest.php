<?php

namespace Tests\Feature\Security;

use App\Models\Plan;
use App\Models\Team;
use App\Models\User;
use App\Support\AntiSpam;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\RateLimiter;
use Tests\Feature\Tim\TimTestCase;

/**
 * Kontrol keamanan lintas modul: header, anti-bot, pemblokir URL
 * mencurigakan, rate limit, dan akses route terproteksi.
 */
class SecurityTest extends TimTestCase
{
    public function test_header_keamanan_ada_di_halaman_html(): void
    {
        $r = $this->get('/admin/login');

        $r->assertOk();
        $r->assertHeader('X-Content-Type-Options', 'nosniff');
        $r->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $r->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $r->assertHeader('Permissions-Policy');
        $r->assertHeaderMissing('X-Powered-By');
        $r->assertHeaderMissing('Cross-Origin-Embedder-Policy');

        $csp = (string) $r->headers->get('Content-Security-Policy');
        $this->assertStringContainsString("default-src 'self'", $csp);
        $this->assertStringContainsString("frame-ancestors 'self'", $csp);
        $this->assertStringContainsString("object-src 'none'", $csp);
        $this->assertStringContainsString('https://fonts.googleapis.com', $csp);
    }

    public function test_header_keamanan_ada_di_json_dan_halaman_galat(): void
    {
        $this->getJson('/api/health')->assertHeader('X-Content-Type-Options', 'nosniff');
        $this->getJson('/api/tidak-ada')->assertNotFound()->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $this->get('/admin/tidak/ada/halamannya')->assertNotFound()->assertHeader('X-Content-Type-Options', 'nosniff');
    }

    public function test_hsts_hanya_bila_diaktifkan_dan_https(): void
    {
        $this->get('/admin/login')->assertHeaderMissing('Strict-Transport-Security');

        config(['security.hsts' => true]);
        $this->get('/admin/login')->assertHeaderMissing('Strict-Transport-Security');
        $this->get('https://localhost/admin/login')->assertHeader('Strict-Transport-Security');
    }

    public function test_honeypot_menolak_bot_di_login(): void
    {
        $this->post('/admin/login', [
            'email' => 'superadmin@gmail.com',
            'password' => '12qwaszx123!!@@##',
            AntiSpam::HONEYPOT => 'http://spam.example',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_time_trap_menolak_kiriman_terlalu_cepat(): void
    {
        config(['security.antispam.time_trap_saat_test' => true]);

        // Stempel "baru saja dimuat" -> ditolak.
        $this->post('/DEMO/login', [
            'email' => 'owner@demo.test',
            'password' => 'password123',
            AntiSpam::WAKTU => AntiSpam::stempel(),
        ])->assertSessionHasErrors('email');
        $this->assertGuest();

        // Stempel 10 detik lalu -> lolos.
        $this->post('/DEMO/login', [
            'email' => 'owner@demo.test',
            'password' => 'password123',
            AntiSpam::WAKTU => Crypt::encryptString((string) (time() - 10)),
        ])->assertRedirect();
        $this->assertAuthenticated();
    }

    public function test_honeypot_dan_time_trap_di_pendaftaran(): void
    {
        $data = [
            'nama_tim' => 'Bot Tim', 'nama' => 'Bot', 'email' => 'bot@x.test', 'no_wa' => '081234567890',
            'password' => 'Rahasia123', 'password_confirmation' => 'Rahasia123', 'peran' => 'owner',
            'plan_id' => Plan::where('kode', 'bulanan')->value('id'),
        ];

        $this->post('/daftar', $data + [AntiSpam::HONEYPOT => 'x'])->assertRedirect(route('beranda'));
        $this->assertFalse(User::where('email', 'bot@x.test')->exists());

        config(['security.antispam.time_trap_saat_test' => true]);
        $this->post('/daftar', $data + [AntiSpam::WAKTU => AntiSpam::stempel()])->assertRedirect(route('beranda'));
        $this->assertFalse(User::where('email', 'bot@x.test')->exists());
    }

    public function test_url_mencurigakan_diblokir_tetapi_isi_form_tidak(): void
    {
        $this->get('/admin/login?q=1%20union%20select%20password%20from%20users')->assertStatus(400);
        $this->get('/admin/login?f=../../../etc/passwd')->assertStatus(400);

        // Isi formulir dengan tanda kutip dan kata SQL tetap sampai ke validasi.
        $this->actingAs($this->owner())
            ->post('/DEMO/proyek', [
                'nama' => "Rumah Pak O'Brien -- select * from union",
                'status' => 'draft',
                'pagu' => 1000000,
            ])->assertStatus(302)->assertSessionHasNoErrors();
    }

    public function test_respon_429_membawa_retry_after(): void
    {
        $kunci = 'tukangsync-test';
        RateLimiter::clear($kunci);

        for ($i = 0; $i < 30; $i++) {
            $r = $this->postJson('/api/auth/login', ['email' => 'x@x.test', 'password' => 'salah']);
            if ($r->status() === 429) {
                $this->assertNotNull($r->headers->get('Retry-After'));

                return;
            }
        }

        $this->fail('Batas laju login API tidak pernah tercapai.');
    }

    public function test_tamu_dialihkan_dari_route_terproteksi(): void
    {
        $this->get('/admin/dashboard')->assertRedirect(route('login'));
        $this->get('/admin/saas/tim')->assertRedirect(route('login'));
        $this->get('/DEMO/dashboard')->assertRedirect(route('tim.login', 'DEMO'));
        $this->getJson('/api/kegiatan')->assertUnauthorized();
    }

    public function test_anggota_tim_tidak_bisa_masuk_panel_admin(): void
    {
        $this->actingAs($this->owner())
            ->get('/admin/saas/tim')
            ->assertRedirect(route('tim.dashboard', 'DEMO'));
    }

    public function test_pengguna_tanpa_izin_ditolak_403(): void
    {
        // Mandor tidak boleh menambah anggota tim.
        $this->actingAs($this->mandor())
            ->post('/DEMO/anggota', ['name' => 'X', 'email' => 'x@y.test', 'peran' => 'anggota', 'password' => 'Rahasia123'])
            ->assertForbidden();
    }

    public function test_avatar_akun_lain_tidak_bisa_diganti(): void
    {
        $admin = User::whereNull('team_id')->firstOrFail();
        $lain = User::create([
            'name' => 'Admin Dua', 'email' => 'admin2@x.test', 'password' => 'Rahasia123', 'is_active' => true,
        ]);

        $this->actingAs($lain)
            ->post("/admin/my-account/{$admin->id}/update-avatar", [
                'avatar' => UploadedFile::fake()->image('a.png'),
            ]);

        $this->assertNull($admin->fresh()->avatar);
    }

    public function test_lupa_password_tidak_membocorkan_email_terdaftar(): void
    {
        $ada = $this->post('/admin/forgot-password', ['email' => 'superadmin@gmail.com']);
        $tidak = $this->post('/admin/forgot-password', ['email' => 'tidak-ada@x.test']);

        $ada->assertSessionHas('status');
        $tidak->assertSessionHas('status');
        $tidak->assertSessionHasNoErrors();
        $this->assertSame(session('status'), $ada->getSession()->get('status') ?? session('status'));
    }

    public function test_team_model_tidak_terpakai_sebagai_lazy_loading_error(): void
    {
        // Pengaman model tidak boleh membuat halaman utama 500.
        $this->actingAs($this->owner())->get('/DEMO/dashboard')->assertOk();
        $this->assertInstanceOf(Team::class, $this->demo);
    }
}
