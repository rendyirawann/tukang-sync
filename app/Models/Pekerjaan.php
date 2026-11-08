<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\JenisKomponen;
use App\Enums\KategoriPekerjaan;
use App\Enums\SatuanVolume;
use App\Models\Concerns\MilikTim;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Satu baris RAB: "Pasang 4 pintu panel 0,9 × 2,1 m di Lantai 1".
 *
 * Volume dihitung dari jumlah × dimensi menurut satuannya. Biaya berasal
 * dari rincian kebutuhan (pekerjaan_material) bila ada; bila tidak --
 * pekerjaan borongan -- dari volume × harga_satuan.
 *
 * @property KategoriPekerjaan $kategori
 * @property JenisKomponen $komponen
 * @property float $jumlah
 * @property float $panjang
 * @property float $lebar
 * @property float $tinggi
 * @property float $volume
 * @property bool $volume_manual
 * @property float $harga_satuan
 * @property int $total
 * @property int $progres
 * @property Carbon|null $rencana_mulai
 * @property Carbon|null $rencana_selesai
 */
class Pekerjaan extends Model
{
    use MilikTim, SoftDeletes;

    protected $table = 'pekerjaan';

    protected $fillable = [
        'team_id', 'kegiatan_id', 'bangunan_id', 'lantai_id', 'ruangan_id', 'analisa_id',
        'kategori', 'komponen', 'nama', 'jumlah', 'panjang', 'lebar', 'tinggi', 'satuan',
        'volume', 'volume_manual', 'harga_satuan', 'progres', 'rencana_mulai', 'rencana_selesai',
        'keterangan', 'urutan', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'kategori' => KategoriPekerjaan::class,
            'komponen' => JenisKomponen::class,
            'jumlah' => 'float',
            'panjang' => 'float',
            'lebar' => 'float',
            'tinggi' => 'float',
            'volume' => 'float',
            'volume_manual' => 'boolean',
            'harga_satuan' => 'float',
            'total' => 'integer',
            'progres' => 'integer',
            'rencana_mulai' => 'date',
            'rencana_selesai' => 'date',
        ];
    }

    /** @return BelongsTo<Kegiatan, $this> */
    public function kegiatan(): BelongsTo
    {
        return $this->belongsTo(Kegiatan::class);
    }

    /** @return BelongsTo<Bangunan, $this> */
    public function bangunan(): BelongsTo
    {
        return $this->belongsTo(Bangunan::class);
    }

    /** @return BelongsTo<Lantai, $this> */
    public function lantai(): BelongsTo
    {
        return $this->belongsTo(Lantai::class);
    }

    /** @return BelongsTo<Ruangan, $this> */
    public function ruangan(): BelongsTo
    {
        return $this->belongsTo(Ruangan::class);
    }

    /** @return BelongsTo<Analisa, $this> */
    public function analisa(): BelongsTo
    {
        return $this->belongsTo(Analisa::class)->withTrashed();
    }

    /** @return HasMany<PekerjaanMaterial, $this> */
    public function kebutuhan(): HasMany
    {
        return $this->hasMany(PekerjaanMaterial::class)->orderBy('kategori')->orderBy('id');
    }

    public function satuanEnum(): ?SatuanVolume
    {
        return SatuanVolume::tryFrom((string) $this->satuan);
    }

    public function satuanSimbol(): string
    {
        return $this->satuanEnum()?->simbol() ?? (string) $this->satuan;
    }

    /** "Bangunan A › Lantai 1 › Dapur" */
    public function lokasiLabel(): string
    {
        return collect([$this->bangunan?->nama, $this->lantai?->nama, $this->ruangan?->nama])
            ->filter()
            ->implode(' › ') ?: 'Umum';
    }

    /** Nilai rupiah pekerjaan yang sudah terlaksana menurut progres. */
    public function nilaiProgres(): int
    {
        return (int) round($this->total * $this->progres / 100);
    }
}
