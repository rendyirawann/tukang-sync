<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\StatusTim;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Tim aktif dengan langganan berjalan sebulan ke depan.
 *
 * @extends Factory<Team>
 */
class TeamFactory extends Factory
{
    protected $model = Team::class;

    public function definition(): array
    {
        return [
            'slug' => fn () => Team::buatSlug(),
            'nama' => 'CV '.fake()->company(),
            'kota' => fake()->city(),
            'email' => fake()->unique()->companyEmail(),
            'no_wa' => '08'.fake()->numerify('##########'),
            'status' => StatusTim::Aktif,
            'lifetime' => false,
            'aktif_sampai' => now()->addMonth(),
            'max_users' => 4,
            'diaktifkan_pada' => now(),
        ];
    }

    public function lifetime(): static
    {
        return $this->state(['lifetime' => true, 'aktif_sampai' => null]);
    }

    public function menunggu(): static
    {
        return $this->state(['status' => StatusTim::Menunggu, 'aktif_sampai' => null, 'diaktifkan_pada' => null]);
    }

    public function ditangguhkan(): static
    {
        return $this->state(['status' => StatusTim::Ditangguhkan]);
    }

    /** Aktif tetapi masa langganannya sudah lewat -> mode hanya baca. */
    public function habis(): static
    {
        return $this->state(['aktif_sampai' => now()->subDay()]);
    }

    public function slug(string $slug): static
    {
        return $this->state(['slug' => $slug]);
    }
}
