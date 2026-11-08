<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tim;

use App\Models\Bangunan;
use App\Models\Kegiatan;
use App\Models\Lantai;
use App\Models\Ruangan;
use App\Support\Izin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** Struktur proyek: bangunan > lantai > ruangan. */
class StrukturController extends TimController
{
    private function kembali(Kegiatan $k, string $pesan): RedirectResponse
    {
        return redirect()->route('tim.proyek.show', [$this->tim()->slug, $k, 'tab' => 'rab'])->with('success', $pesan);
    }

    private function boleh(Request $request): void
    {
        $this->izin(Izin::kelolaRab($request->user()));
    }

    // --- Bangunan ---

    public function storeBangunan(Request $request, $tim, Kegiatan $kegiatan): RedirectResponse
    {
        $this->boleh($request);
        $kegiatan->bangunan()->create($this->dataBangunan($request));

        return $this->kembali($kegiatan, 'Bangunan ditambahkan.');
    }

    public function updateBangunan(Request $request, $tim, Kegiatan $kegiatan, Bangunan $bangunan): RedirectResponse
    {
        $this->boleh($request);
        $this->milik($bangunan->kegiatan_id === $kegiatan->id);
        $bangunan->update($this->dataBangunan($request));

        return $this->kembali($kegiatan, 'Bangunan diperbarui.');
    }

    public function destroyBangunan(Request $request, $tim, Kegiatan $kegiatan, Bangunan $bangunan): RedirectResponse
    {
        $this->boleh($request);
        $this->milik($bangunan->kegiatan_id === $kegiatan->id);
        $bangunan->pekerjaan()->delete();
        $bangunan->delete();

        return $this->kembali($kegiatan, 'Bangunan dihapus beserta item RAB-nya.');
    }

    // --- Lantai ---

    public function storeLantai(Request $request, $tim, Kegiatan $kegiatan, Bangunan $bangunan): RedirectResponse
    {
        $this->boleh($request);
        $this->milik($bangunan->kegiatan_id === $kegiatan->id);
        $bangunan->lantai()->create($this->dataLantai($request));

        return $this->kembali($kegiatan, 'Lantai ditambahkan.');
    }

    public function updateLantai(Request $request, $tim, Kegiatan $kegiatan, Lantai $lantai): RedirectResponse
    {
        $this->boleh($request);
        $this->milik($lantai->bangunan?->kegiatan_id === $kegiatan->id);
        $lantai->update($this->dataLantai($request));

        return $this->kembali($kegiatan, 'Lantai diperbarui.');
    }

    public function destroyLantai(Request $request, $tim, Kegiatan $kegiatan, Lantai $lantai): RedirectResponse
    {
        $this->boleh($request);
        $this->milik($lantai->bangunan?->kegiatan_id === $kegiatan->id);
        $lantai->pekerjaan()->delete();
        $lantai->delete();

        return $this->kembali($kegiatan, 'Lantai dihapus beserta item RAB-nya.');
    }

    // --- Ruangan ---

    public function storeRuangan(Request $request, $tim, Kegiatan $kegiatan, Lantai $lantai): RedirectResponse
    {
        $this->boleh($request);
        $this->milik($lantai->bangunan?->kegiatan_id === $kegiatan->id);
        $lantai->ruangan()->create($this->dataRuangan($request));

        return $this->kembali($kegiatan, 'Ruangan ditambahkan.');
    }

    public function updateRuangan(Request $request, $tim, Kegiatan $kegiatan, Ruangan $ruangan): RedirectResponse
    {
        $this->boleh($request);
        $this->milik($ruangan->lantai?->bangunan?->kegiatan_id === $kegiatan->id);
        $ruangan->update($this->dataRuangan($request));

        return $this->kembali($kegiatan, 'Ruangan diperbarui.');
    }

    public function destroyRuangan(Request $request, $tim, Kegiatan $kegiatan, Ruangan $ruangan): RedirectResponse
    {
        $this->boleh($request);
        $this->milik($ruangan->lantai?->bangunan?->kegiatan_id === $kegiatan->id);
        $ruangan->pekerjaan()->delete();
        $ruangan->delete();

        return $this->kembali($kegiatan, 'Ruangan dihapus beserta item RAB-nya.');
    }

    // --- Validasi ---

    private function dataBangunan(Request $r): array
    {
        return $r->validate([
            'nama' => ['required', 'string', 'max:120'],
            'jenis' => ['required', Rule::in(array_keys(Bangunan::JENIS))],
            'luas_tanah' => ['nullable', 'numeric', 'min:0'],
            'luas_bangunan' => ['nullable', 'numeric', 'min:0'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ]);
    }

    private function dataLantai(Request $r): array
    {
        return $r->validate([
            'nama' => ['required', 'string', 'max:80'],
            'nomor' => ['required', 'integer', 'min:-5', 'max:200'],
            'luas' => ['nullable', 'numeric', 'min:0'],
            'tinggi' => ['nullable', 'numeric', 'min:0'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ]);
    }

    private function dataRuangan(Request $r): array
    {
        return $r->validate([
            'nama' => ['required', 'string', 'max:120'],
            'panjang' => ['nullable', 'numeric', 'min:0'],
            'lebar' => ['nullable', 'numeric', 'min:0'],
            'tinggi' => ['nullable', 'numeric', 'min:0'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ]);
    }
}
