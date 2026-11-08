<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tim;

use App\Models\Kegiatan;
use App\Models\LogHarian;
use App\Support\Izin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LogHarianController extends TimController
{
    public function store(Request $request, $tim, Kegiatan $kegiatan): RedirectResponse
    {
        $this->izin(Izin::isiLogHarian($request->user()));

        $kegiatan->logHarian()->create($this->data($request) + ['created_by' => $request->user()->id]);

        return $this->kembali($kegiatan, 'Log harian dicatat.');
    }

    public function update(Request $request, $tim, Kegiatan $kegiatan, LogHarian $logHarian): RedirectResponse
    {
        $this->cek($request, $kegiatan, $logHarian);
        $logHarian->update($this->data($request));

        return $this->kembali($kegiatan, 'Log harian diperbarui.');
    }

    public function destroy(Request $request, $tim, Kegiatan $kegiatan, LogHarian $logHarian): RedirectResponse
    {
        $this->cek($request, $kegiatan, $logHarian);
        $logHarian->delete();

        return $this->kembali($kegiatan, 'Log harian dihapus.');
    }

    /** Pembuat sendiri atau pengelola. */
    private function cek(Request $request, Kegiatan $kegiatan, LogHarian $log): void
    {
        $this->milik($log->kegiatan_id === $kegiatan->id);
        $u = $request->user();
        $this->izin(Izin::isiLogHarian($u) && ($u->isPengelola() || $log->created_by === $u->id));
    }

    private function kembali(Kegiatan $k, string $pesan): RedirectResponse
    {
        return redirect()->route('tim.proyek.show', [$this->tim()->slug, $k, 'tab' => 'log'])->with('success', $pesan);
    }

    private function data(Request $r): array
    {
        return $r->validate([
            'tanggal' => ['required', 'date'],
            'cuaca' => ['nullable', Rule::in(array_keys(LogHarian::CUACA))],
            'jumlah_pekerja' => ['nullable', 'integer', 'min:0', 'max:5000'],
            'uraian' => ['required', 'string', 'max:5000'],
            'kendala' => ['nullable', 'string', 'max:5000'],
        ]);
    }
}
