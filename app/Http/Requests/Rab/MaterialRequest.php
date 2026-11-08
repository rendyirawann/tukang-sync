<?php

declare(strict_types=1);

namespace App\Http\Requests\Rab;

use App\Models\Material;
use App\Support\Izin;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MaterialRequest extends FormRequest
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
            'nama' => [$wajib, 'string', 'max:150'],
            'kategori' => [$wajib, Rule::in(array_keys(Material::KATEGORI))],
            'satuan' => [$wajib, 'string', 'max:30'],
            'harga' => [$wajib, 'numeric', 'min:0', 'max:999999999999'],
            'merek' => ['nullable', 'string', 'max:80'],
            'toko' => ['nullable', 'string', 'max:120'],
            'keterangan' => ['nullable', 'string', 'max:200'],
            'aktif' => ['nullable', 'boolean'],
        ];
    }
}
