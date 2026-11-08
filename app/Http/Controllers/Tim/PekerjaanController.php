<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tim;

use App\Enums\JenisKomponen;
use App\Enums\KategoriPekerjaan;
use App\Enums\SatuanVolume;
use App\Models\Analisa;
use App\Models\Bangunan;
use App\Models\Kegiatan;
use App\Models\Lantai;
use App\Models\Pekerjaan;
use App\Models\Ruangan;
use App\Services\Rab\RabCalculator;
use App\Support\Izin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PekerjaanController extends TimController
{
    public function __construct(private readonly RabCalculator $rab) {}

    public function create(Request $request, $tim, Kegiatan $kegiatan): View
    {
        $this->izin(Izin::kelolaRab($request->user()));

        $p = new Pekerjaan([
            'bangunan_id' => $request->query('bangunan_id'),
            'lantai_id' => $request->query('lantai_id'),
            'ruangan_id' => $request->query('ruangan_id'),
            'jumlah' => 1,
            'komponen' => JenisKomponen::Lainnya,
            'kategori' => KategoriPekerjaan::Lainnya,
            'satuan' => 'unit',
        ]);

        return $this->form($kegiatan, $p);
    }

    public function store(Request $request, $tim, Kegiatan $kegiatan): RedirectResponse
    {
        $this->izin(Izin::kelolaRab($request->user()));

        $p = new Pekerjaan(['kegiatan_id' => $kegiatan->id, 'created_by' => $request->user()->id]);
        $this->simpan($request, $kegiatan, $p);

        return redirect()->route('tim.proyek.pekerjaan.show', [$this->tim()->slug, $kegiatan, $p])->with('success', 'Item RAB ditambahkan.');
    }

    public function show($tim, Kegiatan $kegiatan, Pekerjaan $pekerjaan): View
    {
        $this->cek($kegiatan, $pekerjaan);
        $pekerjaan->load(['kebutuhan', 'analisa', 'bangunan', 'lantai', 'ruangan']);

        return view('tim.proyek.pekerjaan_show', ['tim' => $this->tim(), 'proyek' => $kegiatan, 'p' => $pekerjaan, 'user' => request()->user()]);
    }

    public function edit(Request $request, $tim, Kegiatan $kegiatan, Pekerjaan $pekerjaan): View
    {
        $this->izin(Izin::kelolaRab($request->user()));
        $this->cek($kegiatan, $pekerjaan);

        return $this->form($kegiatan, $pekerjaan);
    }

    public function update(Request $request, $tim, Kegiatan $kegiatan, Pekerjaan $pekerjaan): RedirectResponse
    {
        $this->izin(Izin::kelolaRab($request->user()));
        $this->cek($kegiatan, $pekerjaan);
        $this->simpan($request, $kegiatan, $pekerjaan);

        return redirect()->route('tim.proyek.pekerjaan.show', [$this->tim()->slug, $kegiatan, $pekerjaan])->with('success', 'Item RAB diperbarui.');
    }

    public function destroy(Request $request, $tim, Kegiatan $kegiatan, Pekerjaan $pekerjaan): RedirectResponse
    {
        $this->izin(Izin::kelolaRab($request->user()));
        $this->cek($kegiatan, $pekerjaan);
        $pekerjaan->delete();

        return redirect()->route('tim.proyek.show', [$this->tim()->slug, $kegiatan, 'tab' => 'rab'])->with('success', 'Item RAB dihapus.');
    }

    public function progres(Request $request, $tim, Kegiatan $kegiatan, Pekerjaan $pekerjaan): RedirectResponse
    {
        $this->izin(Izin::isiProgres($request->user()));
        $this->cek($kegiatan, $pekerjaan);

        $data = $request->validate(['progres' => ['required', 'integer', 'min:0', 'max:100']]);
        $pekerjaan->update(['progres' => $data['progres']]);

        return back()->with('success', "Progres \"{$pekerjaan->nama}\" menjadi {$data['progres']}%.");
    }

    public function segarkanHarga(Request $request, $tim, Kegiatan $kegiatan, Pekerjaan $pekerjaan): RedirectResponse
    {
        $this->izin(Izin::kelolaRab($request->user()));
        $this->cek($kegiatan, $pekerjaan);
        $this->rab->segarkanHarga($pekerjaan);

        return back()->with('success', 'Harga kebutuhan diambil ulang dari katalog.');
    }

    // ------------------------------------------------------------------

    private function cek(Kegiatan $kegiatan, Pekerjaan $p): void
    {
        $this->milik($p->kegiatan_id === $kegiatan->id);
    }

    private function form(Kegiatan $kegiatan, Pekerjaan $p): View
    {
        $kegiatan->load('bangunan.lantai.ruangan');

        return view('tim.proyek.pekerjaan_form', ['tim' => $this->tim(), 'proyek' => $kegiatan, 'p' => $p] + ProyekController::opsiPekerjaan());
    }

    private function simpan(Request $request, Kegiatan $kegiatan, Pekerjaan $p): void
    {
        $data = $request->validate([
            'bangunan_id' => ['nullable', 'integer'],
            'lantai_id' => ['nullable', 'integer'],
            'ruangan_id' => ['nullable', 'integer'],
            'komponen' => ['required', Rule::enum(JenisKomponen::class)],
            'kategori' => ['required', Rule::enum(KategoriPekerjaan::class)],
            'nama' => ['required', 'string', 'max:200'],
            'jumlah' => ['required', 'numeric', 'min:0'],
            'panjang' => ['nullable', 'numeric', 'min:0'],
            'lebar' => ['nullable', 'numeric', 'min:0'],
            'tinggi' => ['nullable', 'numeric', 'min:0'],
            'satuan' => ['required', Rule::enum(SatuanVolume::class)],
            'volume_manual' => ['nullable', 'boolean'],
            'volume' => ['nullable', 'required_if:volume_manual,1', 'numeric', 'min:0'],
            'analisa_id' => ['nullable', 'integer'],
            'harga_satuan' => ['nullable', 'numeric', 'min:0'],
            'progres' => ['nullable', 'integer', 'min:0', 'max:100'],
            'rencana_mulai' => ['nullable', 'date'],
            'rencana_selesai' => ['nullable', 'date'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ]);

        $data += ['bangunan_id' => null, 'lantai_id' => null, 'ruangan_id' => null];

        // Lokasi harus satu rantai di proyek ini.
        $bangunan = $data['bangunan_id'] ? Bangunan::find($data['bangunan_id']) : null;
        $lantai = $data['lantai_id'] ? Lantai::find($data['lantai_id']) : null;
        $ruangan = $data['ruangan_id'] ? Ruangan::find($data['ruangan_id']) : null;

        if (($data['bangunan_id'] && $bangunan?->kegiatan_id !== $kegiatan->id)
            || ($lantai && $lantai->bangunan_id !== $bangunan?->id)
            || ($ruangan && $ruangan->lantai_id !== $lantai?->id)
            || ($data['lantai_id'] && ! $lantai) || ($data['ruangan_id'] && ! $ruangan)) {
            throw ValidationException::withMessages(['bangunan_id' => 'Lokasi (bangunan/lantai/ruangan) tidak cocok dengan proyek ini.']);
        }

        $analisa = ! empty($data['analisa_id']) ? Analisa::find($data['analisa_id']) : null;

        if (! empty($data['analisa_id']) && ! $analisa) {
            throw ValidationException::withMessages(['analisa_id' => 'Analisa tidak ditemukan.']);
        }

        DB::transaction(function () use ($p, $data, $analisa, $request) {
            $analisaBerubah = $analisa && $p->analisa_id !== $analisa->id;
            $manual = $request->boolean('volume_manual');

            $p->fill([
                'bangunan_id' => $data['bangunan_id'] ?: null,
                'lantai_id' => $data['lantai_id'] ?: null,
                'ruangan_id' => $data['ruangan_id'] ?: null,
                'komponen' => $data['komponen'],
                'kategori' => $data['kategori'],
                'nama' => $data['nama'],
                'jumlah' => $data['jumlah'],
                'panjang' => $data['panjang'] ?? null,
                'lebar' => $data['lebar'] ?? null,
                'tinggi' => $data['tinggi'] ?? null,
                'satuan' => $data['satuan'],
                'volume_manual' => $manual,
                'harga_satuan' => $data['harga_satuan'] ?? 0,
                'progres' => $data['progres'] ?? 0,
                'rencana_mulai' => $data['rencana_mulai'] ?? null,
                'rencana_selesai' => $data['rencana_selesai'] ?? null,
                'keterangan' => $data['keterangan'] ?? null,
            ]);

            if ($manual) {
                $p->volume = (float) $data['volume'];
            }

            if (! $analisa && $p->analisa_id) {
                // Analisa dilepas: kembali ke harga borongan.
                $p->analisa_id = null;
                $p->save();
                $p->kebutuhan()->delete();
            }

            $p->save();

            $analisaBerubah ? $this->rab->terapkanAnalisa($p, $analisa) : $this->rab->hitungUlang($p);
        });
    }
}
