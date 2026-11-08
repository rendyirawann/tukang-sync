<?php

declare(strict_types=1);

namespace App\Http\Requests\Rab;

use App\Models\LogHarian;
use App\Support\Izin;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LogHarianRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Izin::isiLogHarian($this->user());
    }

    public function rules(): array
    {
        $wajib = $this->isMethod('POST') ? 'required' : 'sometimes';

        return [
            'tanggal' => [$wajib, 'date'],
            'cuaca' => ['nullable', Rule::in(array_keys(LogHarian::CUACA))],
            'jumlah_pekerja' => ['nullable', 'integer', 'min:0', 'max:10000'],
            'uraian' => [$wajib, 'string', 'max:5000'],
            'kendala' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
