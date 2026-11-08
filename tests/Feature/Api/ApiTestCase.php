<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\Team;
use App\Models\User;
use App\Support\Tenancy\TimAktif;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Dasar test API: setiap test berjalan di dalam satu tim aktif ($this->tim),
 * sehingga factory (Kegiatan, MasterData, User, ...) otomatis menempel ke
 * tim itu -- persis seperti data yang dibuat lewat API.
 *
 * Middleware ResetTimAktif mengosongkan tim aktif di akhir setiap request;
 * call() mengembalikannya supaya factory sesudah request tetap masuk tim
 * yang sama.
 */
abstract class ApiTestCase extends TestCase
{
    use RefreshDatabase;

    protected Team $tim;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tim = Team::factory()->create(['max_users' => 50]);
        TimAktif::set($this->tim);
    }

    protected function tearDown(): void
    {
        TimAktif::reset();

        parent::tearDown();
    }

    public function call($method, $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null)
    {
        $response = parent::call($method, $uri, $parameters, $cookies, $files, $server, $content);

        TimAktif::set($this->tim);

        return $response;
    }

    protected function anggota(string $peran = 'anggota', array $atribut = [], ?Team $tim = null): User
    {
        return User::factory()->forTeam($tim ?? $this->tim, $peran)->create($atribut);
    }

    protected function owner(array $atribut = []): User
    {
        return $this->anggota('owner', $atribut);
    }
}
