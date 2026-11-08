<?php

namespace Database\Factories;

use App\Enums\Peran;
use App\Models\Team;
use App\Models\User;
use App\Support\Tenancy\TimAktif;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * Anggota tim dengan peran tertentu. Tim dibuat baru bila tidak diberi.
     *
     *   User::factory()->forTeam($tim, 'mandor')->create()
     */
    public function forTeam(?Team $team = null, string|Peran $peran = 'owner'): static
    {
        return $this->state(fn () => [
            'team_id' => $team?->id ?? Team::factory(),
            'peran' => $peran instanceof Peran ? $peran->value : $peran,
            'is_active' => true,
        ]);
    }

    public function nonaktif(): static
    {
        return $this->state(['is_active' => false]);
    }

    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // Mengikuti tim aktif (test API); tanpa tim aktif = akun platform.
            'team_id' => fn () => TimAktif::get()?->id,
            'peran' => fn (array $a) => $a['team_id'] ? 'anggota' : null,
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            // Ditulis eksplisit: default kolom tidak ikut terisi di objek hasil
            // create(), dan middleware membaca is_active dari objek itu.
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
