<?php

namespace Tests\Feature\Seo;

use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\SaasSeeder;
use Database\Seeders\SuperAdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([RolePermissionSeeder::class, SuperAdminSeeder::class, SaasSeeder::class]);
    }

    public function test_beranda_punya_meta_seo_lengkap(): void
    {
        $this->get('/')->assertOk()
            ->assertSee('<title>'.config('app.name').' — ', false)
            ->assertSee('<meta name="description"', false)
            ->assertSee('<link rel="canonical" href="'.url('/').'"', false)
            ->assertSee('property="og:title"', false)
            ->assertSee('property="og:image"', false)
            ->assertSee('name="twitter:card" content="summary_large_image"', false)
            ->assertSee('application/ld+json', false)
            ->assertSee('"WebApplication"', false)
            ->assertSee('name="csrf-token"', false);
    }

    public function test_beranda_hanya_satu_h1(): void
    {
        $html = $this->get('/')->getContent();
        $this->assertSame(1, substr_count($html, '<h1'));
    }

    public function test_robots_txt_dan_sitemap(): void
    {
        $this->get('/robots.txt')->assertOk()
            ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
            ->assertSee('Sitemap: '.url('sitemap.xml'));

        $this->get('/sitemap.xml')->assertOk()
            ->assertSee('<loc>'.route('beranda').'</loc>', false)
            ->assertSee('<loc>'.route('daftar').'</loc>', false);
    }

    public function test_robots_production_menolak_area_privat(): void
    {
        $this->app['env'] = 'production';

        $this->get('/robots.txt')->assertOk()->assertSee('Disallow: /admin')->assertSee('Disallow: /api');
    }

    public function test_noindex_di_halaman_login_admin_dan_lingkungan_non_production(): void
    {
        $this->get('/admin/login')->assertHeader('X-Robots-Tag', 'noindex, nofollow');
        // APP_ENV=testing: beranda pun noindex.
        $this->get('/')->assertHeader('X-Robots-Tag', 'noindex, nofollow');
    }

    public function test_beranda_boleh_diindeks_di_production(): void
    {
        $this->app['env'] = 'production';

        $this->get('/')->assertOk()->assertHeaderMissing('X-Robots-Tag');
        $this->get('/admin/login')->assertHeader('X-Robots-Tag', 'noindex, nofollow');
    }

    public function test_favicon_dan_manifest_ada(): void
    {
        foreach (['favicon.ico', 'favicon.svg', 'favicon-32x32.png', 'apple-touch-icon.png', 'android-chrome-192x192.png', 'android-chrome-512x512.png', 'og-image.png', 'site.webmanifest', 'logo.svg', 'logo-icon.svg', 'logo-light.svg'] as $f) {
            $this->assertFileExists(public_path('assets/media/branding/'.$f));
        }

        $manifest = json_decode(file_get_contents(public_path('assets/media/branding/site.webmanifest')), true);
        $this->assertSame('Tukangsync', $manifest['name']);
        $this->assertSame('#1E3A5F', $manifest['theme_color']);
    }
}
