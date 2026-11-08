<?php

namespace Tests\Feature\Errors;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Tests\TestCase;

/** Halaman galat web, JSON galat AJAX, dan validasi 422. */
class HalamanGalatTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Rute sementara khusus tes; tidak pernah didaftarkan di aplikasi.
        Route::middleware('web')->group(function () {
            Route::get('/_uji/galat/{kode}', function (int $kode) {
                abort($kode);
            });
            Route::get('/_uji/pesan-403', fn () => abort(403, 'Hanya owner yang boleh membuka laporan ini.'));
            Route::get('/_uji/pesan-500', fn () => throw new \RuntimeException('SQLSTATE rahasia'));
            Route::get('/_uji/419', fn () => throw new TokenMismatchException);
            Route::get('/_uji/429', fn () => throw new TooManyRequestsHttpException(42));
            Route::post('/_uji/validasi', function (Request $request) {
                $request->validate(['nama' => ['required']]);

                return 'ok';
            });
        });
    }

    public static function kodeProvider(): array
    {
        return [
            [400, 'Permintaan Tidak Valid'],
            [401, 'Perlu Masuk'],
            [403, 'Anda tidak memiliki akses ke halaman ini.'],
            [404, 'Halaman Tidak Ditemukan'],
            [405, 'Metode Tidak Diizinkan'],
            [408, 'Waktu Permintaan Habis'],
            [413, 'Ukuran file terlalu besar.'],
            [422, 'Data Tidak Valid'],
            [500, 'Terjadi Kesalahan'],
            [502, 'Gateway Bermasalah'],
            [503, 'Sedang Dalam Pemeliharaan'],
            [504, 'Waktu Tunggu Server Habis'],
        ];
    }

    #[DataProvider('kodeProvider')]
    public function test_setiap_kode_galat_tampil_dengan_pesan_indonesia(int $kode, string $teks): void
    {
        $this->get("/_uji/galat/{$kode}")
            ->assertStatus($kode)
            ->assertSee($teks)
            ->assertSee('Beranda')
            ->assertSee('noindex, nofollow', false);
    }

    public function test_pesan_abort_developer_ditampilkan_untuk_4xx(): void
    {
        $this->get('/_uji/pesan-403')->assertStatus(403)->assertSee('Hanya owner yang boleh membuka laporan ini.');
    }

    public function test_detail_exception_tidak_bocor_di_500(): void
    {
        config(['app.debug' => false]);

        $this->get('/_uji/pesan-500')->assertStatus(500)->assertDontSee('SQLSTATE')->assertSee('Terjadi Kesalahan');
    }

    public function test_halaman_419_menawarkan_muat_ulang(): void
    {
        $this->get('/_uji/419')->assertStatus(419)
            ->assertSee('Sesi Anda telah kedaluwarsa. Silakan muat ulang halaman dan coba lagi.')
            ->assertSee('Muat ulang');
    }

    public function test_halaman_429_menyebut_detik_tunggu(): void
    {
        $this->get('/_uji/429')->assertStatus(429)->assertSee('Coba lagi dalam 42 detik.');
    }

    public function test_validasi_web_redirect_dengan_galat_per_kolom(): void
    {
        $this->from('/daftar')->post('/_uji/validasi', [])
            ->assertRedirect('/daftar')
            ->assertSessionHasErrors(['nama' => 'Kolom Nama Kegiatan wajib diisi.']);
    }

    public function test_validasi_ajax_web_mengembalikan_json_422(): void
    {
        $this->postJson('/_uji/validasi', [])
            ->assertStatus(422)
            ->assertJsonPath('status', 'error')
            ->assertJsonStructure(['status', 'message', 'errors' => ['nama']]);
    }

    public function test_galat_ajax_web_berbentuk_json(): void
    {
        $this->getJson('/_uji/galat/403')->assertStatus(403)->assertJson(['status' => 'error']);
        $this->getJson('/_uji/419')->assertStatus(419)->assertJsonPath('message', 'Sesi kedaluwarsa, silakan muat ulang halaman.');
    }

    public function test_envelope_api_tidak_berubah(): void
    {
        $this->getJson('/api/tidak-ada')->assertStatus(404)
            ->assertJson(['success' => false, 'code' => 'NOT_FOUND']);
    }

    public function test_halaman_tidak_dikenal_404_web(): void
    {
        $this->get('/admin/halaman-yang-tidak-ada')->assertStatus(404)->assertSee('Halaman Tidak Ditemukan');
    }
}
