<?php

declare(strict_types=1);

namespace App\Http\Requests\Rab;

use App\Enums\JenisKomponen;
use App\Enums\KategoriPekerjaan;
use App\Support\Izin;
use App\Support\Tenancy\TimAktif;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Exists;

class PekerjaanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Izin::kelolaRab($this->user());
    }

    /** exists yang dibatasi ke tim aktif dan baris yang belum dihapus. */
    public static function milikTim(string $tabel): Exists
    {
        return Rule::exists($tabel, 'id')->where('team_id', TimAktif::id())->whereNull('deleted_at');
    }

    public function rules(): array
    {
        $ubah = $this->isMethod('PUT') || $this->isMethod('PATCH');
        $wajib = $ubah ? 'sometimes' : 'required';

        return [
            'kategori' => [$wajib, Rule::enum(KategoriPekerjaan::class)],
            'komponen' => ['nullable', Rule::enum(JenisKomponen::class)],
            'nama' => [$wajib, 'string', 'max:200'],
            'bangunan_id' => ['nullable', 'integer', self::milikTim('bangunan')],
            'lantai_id' => ['nullable', 'integer', self::milikTim('lantai')],
            'ruangan_id' => ['nullable', 'integer', self::milikTim('ruangan')],
            'analisa_id' => ['nullable', 'integer', self::milikTim('analisa')],
            'jumlah' => [$wajib, 'numeric', 'min:0', 'max:99999999'],
            'panjang' => ['nullable', 'numeric', 'min:0', 'max:999999'],
            'lebar' => ['nullable', 'numeric', 'min:0', 'max:999999'],
            'tinggi' => ['nullable', 'numeric', 'min:0', 'max:999999'],
            'satuan' => [$wajib, 'string', 'max:20'],
            'volume_manual' => ['nullable', 'boolean'],
            'volume' => ['nullable', 'required_if_accepted:volume_manual', 'numeric', 'min:0'],
            'harga_satuan' => ['nullable', 'numeric', 'min:0'],
            'progres' => ['nullable', 'integer', 'min:0', 'max:100'],
            'rencana_mulai' => ['nullable', 'date'],
            'rencana_selesai' => ['nullable', 'date', 'after_or_equal:rencana_mulai'],
            'keterangan' => ['nullable', 'string', 'max:255'],
            'urutan' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function attributes(): array
    {
        return ['nama' => 'Nama pekerjaan', 'jumlah' => 'Jumlah', 'satuan' => 'Satuan', 'kategori' => 'Kategori'];
    }
}
