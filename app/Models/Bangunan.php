<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\MilikTim;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bangunan extends Model
{
    use MilikTim, SoftDeletes;

    public const JENIS = [
        'rumah' => 'Rumah Tinggal',
        'ruko' => 'Ruko',
        'gedung' => 'Gedung / Kantor',
        'gudang' => 'Gudang / Workshop',
        'sekolah' => 'Sekolah / Fasilitas Umum',
        'masjid' => 'Tempat Ibadah',
        'pagar' => 'Pagar / Talud',
        'jalan' => 'Jalan / Drainase',
        'renovasi' => 'Renovasi',
        'lainnya' => 'Lainnya',
    ];

    protected $table = 'bangunan';

    protected $fillable = [
        'team_id', 'kegiatan_id', 'nama', 'jenis', 'luas_tanah', 'luas_bangunan', 'keterangan', 'urutan',
    ];

    protected function casts(): array
    {
        return ['luas_tanah' => 'float', 'luas_bangunan' => 'float'];
    }

    public function kegiatan(): BelongsTo
    {
        return $this->belongsTo(Kegiatan::class);
    }

    public function lantai(): HasMany
    {
        return $this->hasMany(Lantai::class)->orderBy('nomor')->orderBy('id');
    }

    public function pekerjaan(): HasMany
    {
        return $this->hasMany(Pekerjaan::class);
    }

    public function jenisLabel(): string
    {
        return self::JENIS[$this->jenis] ?? ucfirst((string) $this->jenis);
    }
}
