<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\MilikTim;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/** Catatan harian lapangan dari mandor/pengawas. */
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

    public function kegiatan(): BelongsTo
    {
        return $this->belongsTo(Kegiatan::class);
    }

    public function pembuat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
