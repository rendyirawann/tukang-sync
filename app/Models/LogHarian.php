<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\MilikTim;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Catatan harian lapangan dari mandor/pengawas.
 *
 * @property Carbon|null $tanggal
 * @property int $jumlah_pekerja
 */
class LogHarian extends Model
{
    use MilikTim, SoftDeletes;

    public const CUACA = ['cerah' => 'Cerah', 'berawan' => 'Berawan', 'hujan' => 'Hujan', 'hujan_lebat' => 'Hujan lebat'];

    protected $table = 'log_harian';

    protected $fillable = [
        'team_id', 'kegiatan_id', 'tanggal', 'cuaca', 'jumlah_pekerja', 'uraian', 'kendala', 'created_by',
    ];

    protected function casts(): array
    {
        return ['tanggal' => 'date', 'jumlah_pekerja' => 'integer'];
    }

    /** @return BelongsTo<Kegiatan, $this> */
    public function kegiatan(): BelongsTo
    {
        return $this->belongsTo(Kegiatan::class);
    }

    /** @return BelongsTo<User, $this> */
    public function pembuat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
