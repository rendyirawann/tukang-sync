<?php

namespace Tests\Feature\Tim;

use App\Enums\Peran;
use App\Models\Plan;
use App\Models\Team;
use App\Models\User;
use App\Services\Saas\PenyediaTim;
use Database\Seeders\DemoTimSeeder;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\SaasSeeder;
use Database\Seeders\SuperAdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

abstract class TimTestCase extends TestCase
{
    use RefreshDatabase;

    protected Team $demo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([RolePermissionSeeder::class, SuperAdminSeeder::class, SaasSeeder::class, DemoTimSeeder::class]);
        $this->demo = Team::where('slug', 'DEMO')->firstOrFail();
    }

    protected function owner(): User
    {
        return User::where('email', 'owner@demo.test')->firstOrFail();
    }

    protected function mandor(): User
    {
        return User::where('email', 'mandor@demo.test')->firstOrFail();
    }

    protected function anggota(Team $tim, Peran $peran, string $email): User
    {
        return User::create([
            'team_id' => $tim->id, 'peran' => $peran->value, 'name' => ucfirst($peran->value),
            'email' => $email, 'password' => 'password123', 'is_active' => true,
        ]);
    }

    /** Tim kedua yang sudah aktif. */
    protected function timLain(): Team
    {
        $p = app(PenyediaTim::class);
        $bayar = $p->daftar([
            'nama_tim' => 'Tim Lain', 'nama' => 'Lain', 'email' => 'lain@x.test', 'no_wa' => '08123456789',
            'password' => 'password123', 'peran' => 'owner',
        ], Plan::where('kode', 'bulanan')->firstOrFail());
        $p->konfirmasi($bayar, User::whereNull('team_id')->firstOrFail());

        return $bayar->team->fresh();
    }

    protected function url(string $path = ''): string
    {
        return '/DEMO'.($path !== '' ? '/'.$path : '');
    }
}
