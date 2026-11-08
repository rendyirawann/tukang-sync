<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tim;

use App\Enums\JenisKomponen;
use App\Enums\KategoriPekerjaan;
use App\Enums\SatuanVolume;
use App\Enums\StatusKegiatan;
use App\Models\Analisa;
use App\Models\Bangunan;
use App\Models\Kegiatan;
use App\Models\LogHarian;
use App\Models\Material;
use App\Services\Rab\RabCalculator;
use App\Support\Izin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProyekController extends TimController
{
    public function index(Request $request): View
    {
        $cari = trim((string) $request->query('q'));
        $status = (string) $request->query('status');

        $proyek = Kegiatan::query()
            ->withSum('pekerjaan as rab_total', 'total')
            ->when(StatusKegiatan::tryFrom($status), fn ($q) => $q->where('status', $status))
            ->when($cari !== '', function ($q) use ($cari) {
                $like = '%'.mb_strtolower($cari).'%';
                $q->where(fn ($w) => $w->whereRaw('LOWER(nama) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(kode) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(lokasi) LIKE ?', [$like]));
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('tim.proyek.index', ['tim' => $this->tim(), 'proyek' => $proyek, 'cari' => $cari, 'status' => $status]);
    }

    public function create(): View
    {
        $this->izin(Izin::kelolaKegiatan(request()->user()));

        return view('tim.proyek.form', ['tim' => $this->tim(), 'proyek' => new Kegiatan(['status' => 'draft'])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->izin(Izin::kelolaKegiatan($request->user()));

        $proyek = new Kegiatan($this->data($request) + config('taksasi.default_rates', []));
        $proyek->created_by = $request->user()->id;
        $proyek->updated_by = $request->user()->id;
        $proyek->save();
        $proyek->recalculate();

        return redirect()->route('tim.proyek.show', [$this->tim()->slug, $proyek])->with('success', 'Proyek dibuat. Mulai susun RAB-nya di tab Struktur & RAB.');
    }

    public function show(Request $request, $tim,Kegiatan $kegiatan, RabCalculator $rab): View
    {
        $tab = (string) $request->query('tab', 'ringkasan');

        $kegiatan->load(['bangunan.lantai.ruangan']);

        $pekerjaan = $kegiatan->pekerjaan()->with(['bangunan:id,nama', 'lantai:id,nama', 'ruangan:id,nama'])
            ->orderBy('urutan')->orderBy('id')->get();

        return view('tim.proyek.show', [
            'tim' => $this->tim(),
            'proyek' => $kegiatan,
            'tab' => $tab,
            'ringkasan' => $rab->ringkasan($kegiatan),
            'pekerjaan' => $pekerjaan,
            'rekap' => $tab === 'material' ? $rab->rekapMaterial($kegiatan) : collect(),
            'log' => $tab === 'log' ? LogHarian::where('kegiatan_id', $kegiatan->id)->with('pembuat:id,name')->latest('tanggal')->latest('id')->paginate(20)->withQueryString() : null,
            'belanja' => $tab === 'belanja' ? $kegiatan->bahanBakuItems()->latest('tanggal_beli')->latest('id')->paginate(30)->withQueryString() : null,
            'materials' => $tab === 'belanja' ? Material::aktif()->get(['id', 'nama', 'satuan', 'harga']) : collect(),
            'jenisBangunan' => Bangunan::JENIS,
            'cuaca' => LogHarian::CUACA,
            'user' => $request->user(),
        ]);
    }

    public function edit($tim,Kegiatan $kegiatan): View
    {
        $this->izin(Izin::kelolaKegiatan(request()->user()));

        return view('tim.proyek.form', ['tim' => $this->tim(), 'proyek' => $kegiatan]);
    }

    public function update(Request $request, $tim,Kegiatan $kegiatan): RedirectResponse
    {
        $this->izin(Izin::kelolaKegiatan($request->user()));

        $kegiatan->fill($this->data($request, $kegiatan));
        $kegiatan->updated_by = $request->user()->id;
        $kegiatan->save();
        $kegiatan->recalculate();

        return redirect()->route('tim.proyek.show', [$this->tim()->slug, $kegiatan])->with('success', 'Proyek diperbarui.');
    }

    public function destroy(Request $request, $tim,Kegiatan $kegiatan): RedirectResponse
    {
        $this->izin(Izin::kelolaKegiatan($request->user()));
        $kegiatan->delete();

        return redirect()->route('tim.proyek.index', $this->tim()->slug)->with('success', 'Proyek dihapus.');
    }

    /** @return array<string, mixed> */
    private function data(Request $request, ?Kegiatan $proyek = null): array
    {
        $timId = $this->tim()->id;

        return $request->validate([
            'kode' => ['nullable', 'string', 'max:40', Rule::unique('kegiatan', 'kode')->where('team_id', $timId)->whereNull('deleted_at')->ignore($proyek?->id)],
            'nama' => ['required', 'string', 'max:150'],
            'lokasi' => ['nullable', 'string', 'max:150'],
            'keterangan' => ['nullable', 'string', 'max:2000'],
            'sumber_dana' => ['nullable', 'string', 'max:100'],
            'tanggal_mulai' => ['nullable', 'date'],
            'tanggal_selesai' => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],
            'status' => ['required', Rule::enum(StatusKegiatan::class)],
            'pagu' => ['nullable', 'integer', 'min:0'],
        ]) + ['pagu' => 0];
    }

    /** Opsi untuk form pekerjaan (dipakai PekerjaanController). */
    public static function opsiPekerjaan(): array
    {
        return [
            'kategori' => KategoriPekerjaan::opsi(),
            'komponen' => collect(JenisKomponen::cases())->mapWithKeys(fn ($k) => [$k->value => [
                'label' => $k->label(), 'satuan' => $k->satuanBawaan(), 'kategori' => $k->kategoriBawaan()->value,
            ]])->all(),
            'satuan' => collect(SatuanVolume::cases())->mapWithKeys(fn ($s) => [$s->value => [
                'label' => $s->label(), 'dimensi' => $s->dimensi(), 'otomatis' => $s->otomatis(),
            ]])->all(),
            'analisa' => Analisa::query()->orderBy('kategori')->orderBy('nama')->get(['id', 'nama', 'satuan', 'kategori']),
        ];
    }
}
