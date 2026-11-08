<?php

declare(strict_types=1);

namespace App\Http\Requests\Rab;

use App\Enums\KategoriPekerjaan;
use App\Support\Izin;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AnalisaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Izin::kelolaKatalog($this->user());
    }

    public function rules(): array
    {
        $wajib = $this->isMethod('POST') ? 'required' : 'sometimes';

        return [
            'kode' => ['nullable', 'string', 'max:30'],
            'nama' => [$wajib, 'string', 'max:200'],
            'kategori' => [$wajib, Rule::enum(KategoriPekerjaan::class)],
            'satuan' => [$wajib, 'string', 'max:20'],
            'keterangan' => ['nullable', 'string', 'max:255'],
            'items' => ['sometimes', 'array', 'max:100'],
            'items.*.material_id' => ['required', 'integer', PekerjaanRequest::milikTim('materials')],
            'items.*.koefisien' => ['required', 'numeric', 'gt:0', 'max:999999'],
        ];
    }
}
