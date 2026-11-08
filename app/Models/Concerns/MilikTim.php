<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use App\Models\Team;
use App\Support\Tenancy\TimAktif;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Menjadikan model milik satu tim.
 *
 * - Query otomatis disaring ke tim aktif (TimAktif::id()).
 * - team_id diisi otomatis saat membuat baris baru.
 * - Tanpa tim aktif (superadmin di panel /admin, console, seeder) tidak ada
 *   penyaringan -- superadmin memang melihat semua tim.
 */
trait MilikTim
{
    public static function bootMilikTim(): void
    {
        static::addGlobalScope('tim', function (Builder $query): void {
            $timId = TimAktif::id();

            if ($timId !== null) {
                $query->where($query->getModel()->qualifyColumn('team_id'), $timId);
            }
        });

        static::creating(function ($model): void {
            if (empty($model->team_id)) {
                $model->team_id = TimAktif::id();
            }
        });
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
