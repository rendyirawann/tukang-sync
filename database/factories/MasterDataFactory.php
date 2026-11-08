<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\JenisMaster;
use App\Models\MasterData;
use App\Models\Team;
use App\Support\Tenancy\TimAktif;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MasterData>
 */
class MasterDataFactory extends Factory
{
    protected $model = MasterData::class;

    public function definition(): array
    {
        return [
            'team_id' => fn () => TimAktif::id() ?? Team::factory(),
            'jenis' => JenisMaster::Satuan->value,
            'nama' => fake()->unique()->word(),
            'keterangan' => null,
            'urutan' => 0,
            'aktif' => true,
        ];
    }

    public function jenis(JenisMaster $jenis): static
    {
        return $this->state(['jenis' => $jenis->value]);
    }

    public function nonaktif(): static
    {
        return $this->state(['aktif' => false]);
    }
}
