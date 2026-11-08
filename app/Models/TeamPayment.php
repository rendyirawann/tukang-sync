<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\StatusPembayaran;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * @property StatusPembayaran $status
 * @property int $nominal
 * @property Carbon|null $periode_mulai
 * @property Carbon|null $periode_selesai
 * @property Carbon|null $dikonfirmasi_pada
 */
class TeamPayment extends Model
{
    protected $fillable = [
        'kode', 'team_id', 'plan_id', 'jenis', 'nominal', 'status', 'bank_account_id',
        'bukti_path', 'bukti_mime', 'catatan', 'alasan_tolak', 'periode_mulai',
        'periode_selesai', 'dikonfirmasi_oleh', 'dikonfirmasi_pada',
    ];

    protected function casts(): array
    {
        return [
            'status' => StatusPembayaran::class,
            'nominal' => 'integer',
            'periode_mulai' => 'datetime',
            'periode_selesai' => 'datetime',
            'dikonfirmasi_pada' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $p): void {
            $p->kode ??= self::buatKode();
        });
    }

    public static function buatKode(): string
    {
        do {
            $kode = 'INV-'.now()->format('ymd').'-'.Str::upper(Str::random(4));
        } while (self::where('kode', $kode)->exists());

        return $kode;
    }

    /** @return BelongsTo<Team, $this> */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class)->withTrashed();
    }

    /** @return BelongsTo<Plan, $this> */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    /** @return BelongsTo<BankAccount, $this> */
    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }

    /** @return BelongsTo<User, $this> */
    public function konfirmator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dikonfirmasi_oleh');
    }

    public function isMenunggu(): bool
    {
        return $this->status === StatusPembayaran::Menunggu;
    }

    public function punyaBukti(): bool
    {
        return $this->bukti_path !== null && Storage::disk('local')->exists($this->bukti_path);
    }

    public function buktiPdf(): bool
    {
        return $this->bukti_mime === 'application/pdf';
    }

    public function nominalLabel(): string
    {
        return 'Rp '.number_format($this->nominal, 0, ',', '.');
    }

    public function jenisLabel(): string
    {
        return $this->jenis === 'perpanjangan' ? 'Perpanjangan' : 'Pendaftaran';
    }
}
