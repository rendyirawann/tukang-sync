<?php

declare(strict_types=1);

namespace App\Services\Saas;

use App\Enums\Peran;
use App\Enums\StatusPembayaran;
use App\Enums\StatusTim;
use App\Models\Plan;
use App\Models\Team;
use App\Models\TeamPayment;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Siklus hidup tim: daftar -> (bayar) -> dikonfirmasi superadmin -> aktif
 * -> perpanjang. Dipakai formulir pendaftaran publik dan panel superadmin,
 * supaya kedua jalur menghasilkan data yang persis sama.
 */
final class PenyediaTim
{
    public const MIME_BUKTI = ['image/png', 'image/jpeg', 'application/pdf'];

    public function __construct(private readonly KatalogAwal $katalog) {}

    /**
     * Mendaftarkan tim baru beserta akun penanggung jawabnya.
     *
     * Tim dibuat berstatus "menunggu" dan akun owner nonaktif sampai
     * pembayarannya dikonfirmasi. Kode tim (slug) langsung dipesan, supaya
     * pendaftar bisa menyebutnya saat konfirmasi lewat WhatsApp.
     *
     * @param  array{nama_tim:string, kota?:?string, alamat?:?string, nama:string, email:string, no_wa:string, password:string, peran:string}  $data
     */
    public function daftar(array $data, Plan $plan): TeamPayment
    {
        return DB::transaction(function () use ($data, $plan) {
            $tim = Team::create([
                'slug' => Team::buatSlug(),
                'nama' => $data['nama_tim'],
                'kota' => $data['kota'] ?? null,
                'alamat' => $data['alamat'] ?? null,
                'email' => $data['email'],
                'no_wa' => $data['no_wa'],
                'status' => StatusTim::Menunggu,
                'plan_id' => $plan->id,
                'max_users' => $plan->max_users,
            ]);

            User::create([
                'team_id' => $tim->id,
                'peran' => Peran::tryFrom($data['peran'])?->pengelola() ? $data['peran'] : Peran::Owner->value,
                'name' => $data['nama'],
                'email' => Str::lower($data['email']),
                'no_wa' => $data['no_wa'],
                'password' => $data['password'],
                'is_active' => false,
            ]);

            $this->katalog->isi($tim);

            return $tim->payments()->create([
                'plan_id' => $plan->id,
                'jenis' => 'pendaftaran',
                'nominal' => $plan->harga,
                'status' => StatusPembayaran::Menunggu,
            ]);
        });
    }

    /** Tagihan perpanjangan atau ganti paket untuk tim yang sudah ada. */
    public function tagihan(Team $tim, Plan $plan): TeamPayment
    {
        $lama = $tim->payments()->where('status', StatusPembayaran::Menunggu)->where('jenis', 'perpanjangan')->first();

        if ($lama) {
            $lama->update(['plan_id' => $plan->id, 'nominal' => $plan->harga]);

            return $lama;
        }

        return $tim->payments()->create([
            'plan_id' => $plan->id,
            'jenis' => 'perpanjangan',
            'nominal' => $plan->harga,
            'status' => StatusPembayaran::Menunggu,
        ]);
    }

    /**
     * Superadmin menerima pembayaran: masa langganan diterapkan, tim dan
     * akun pengelolanya diaktifkan.
     */
    public function konfirmasi(TeamPayment $bayar, User $admin, ?string $catatan = null, ?int $bankAccountId = null): TeamPayment
    {
        if (! $bayar->isMenunggu()) {
            throw ValidationException::withMessages(['status' => 'Pembayaran ini sudah diproses sebelumnya.']);
        }

        return DB::transaction(function () use ($bayar, $admin, $catatan, $bankAccountId) {
            $tim = $bayar->team;
            $plan = $bayar->plan ?? $tim->plan;

            [$mulai, $selesai] = $tim->terapkanPaket($plan);

            // Saat pendaftaran pertama, pengelola diaktifkan. Anggota yang
            // sengaja dinonaktifkan pemilik tim tidak ikut dihidupkan.
            if ($bayar->jenis === 'pendaftaran') {
                $tim->users()
                    ->whereIn('peran', [Peran::Owner->value, Peran::Kontraktor->value])
                    ->update(['is_active' => true]);
            }

            $bayar->update([
                'status' => StatusPembayaran::Diterima,
                'periode_mulai' => $mulai,
                'periode_selesai' => $selesai,
                'dikonfirmasi_oleh' => $admin->id,
                'dikonfirmasi_pada' => now(),
                'catatan' => $catatan ?? $bayar->catatan,
                'bank_account_id' => $bankAccountId ?? $bayar->bank_account_id,
            ]);

            return $bayar->fresh();
        });
    }

    public function tolak(TeamPayment $bayar, User $admin, string $alasan): TeamPayment
    {
        if (! $bayar->isMenunggu()) {
            throw ValidationException::withMessages(['status' => 'Pembayaran ini sudah diproses sebelumnya.']);
        }

        $bayar->update([
            'status' => StatusPembayaran::Ditolak,
            'alasan_tolak' => $alasan,
            'dikonfirmasi_oleh' => $admin->id,
            'dikonfirmasi_pada' => now(),
        ]);

        return $bayar;
    }

    /**
     * Menyimpan bukti transfer dari unggahan berkas ATAU gambar yang
     * ditempel (Ctrl+V) -- yang dikirim browser sebagai data URL base64.
     */
    public function simpanBukti(TeamPayment $bayar, ?UploadedFile $berkas, ?string $dataUrl): void
    {
        [$isi, $mime] = $berkas !== null
            ? [$berkas->get(), $berkas->getMimeType()]
            : $this->bacaDataUrl((string) $dataUrl);

        if (! in_array($mime, self::MIME_BUKTI, true)) {
            throw ValidationException::withMessages(['bukti' => 'Bukti harus berupa PNG, JPG, atau PDF.']);
        }

        if (strlen($isi) > 5 * 1024 * 1024) {
            throw ValidationException::withMessages(['bukti' => 'Ukuran bukti maksimal 5 MB.']);
        }

        $ext = match ($mime) {
            'image/png' => 'png',
            'image/jpeg' => 'jpg',
            default => 'pdf',
        };

        // Disk "local" (private): bukti transfer memuat data rekening
        // pelanggan, jadi hanya bisa dibuka lewat rute superadmin.
        $path = 'bukti-bayar/'.$bayar->team_id.'/'.$bayar->kode.'-'.Str::random(6).'.'.$ext;
        Storage::disk('local')->put($path, $isi);

        if ($bayar->bukti_path) {
            Storage::disk('local')->delete($bayar->bukti_path);
        }

        $bayar->update(['bukti_path' => $path, 'bukti_mime' => $mime]);
    }

    /** @return array{0: string, 1: string} */
    private function bacaDataUrl(string $dataUrl): array
    {
        if (! preg_match('#^data:([a-z/+.-]+);base64,(.+)$#i', $dataUrl, $m)) {
            throw ValidationException::withMessages(['bukti' => 'Gambar yang ditempel tidak terbaca. Coba unggah berkasnya.']);
        }

        $isi = base64_decode($m[2], true);

        if ($isi === false) {
            throw ValidationException::withMessages(['bukti' => 'Gambar yang ditempel rusak. Coba unggah berkasnya.']);
        }

        // Jenis berkas ditentukan dari isinya, bukan dari label data URL
        // yang bisa ditulis apa saja oleh pengirim.
        $mime = (new \finfo(FILEINFO_MIME_TYPE))->buffer($isi) ?: 'application/octet-stream';

        return [$isi, $mime];
    }
}
