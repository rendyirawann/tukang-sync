<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tim;

use App\Enums\KategoriPekerjaan;
use App\Enums\SatuanVolume;
use App\Models\Analisa;
use App\Models\Material;
use App\Support\Izin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AnalisaController extends TimController
{
    public function index(Request $request): View
    {
        $analisa = Analisa::query()->with('items.material')->orderBy('kategori')->orderBy('nama')->get();

        return view('tim.katalog.analisa', [
            'tim' => $this->tim(), 'analisa' => $analisa, 'boleh' => Izin::kelolaKatalog($request->user()),
        ]);
    }

    public function create(Request $request): View
    {
        $this->izin(Izin::kelolaKatalog($request->user()));

        return $this->form(new Analisa(['satuan' => 'm2']));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->izin(Izin::kelolaKatalog($request->user()));
        $this->simpan($request, new Analisa);

        return redirect()->route('tim.analisa.index', $this->tim()->slug)->with('success', 'Analisa disimpan.');
    }

    public function edit(Request $request, $tim, Analisa $analisa): View
    {
        $this->izin(Izin::kelolaKatalog($request->user()));

        return $this->form($analisa->load('items'));
    }

    public function update(Request $request, $tim, Analisa $analisa): RedirectResponse
    {
        $this->izin(Izin::kelolaKatalog($request->user()));
        $this->simpan($request, $analisa);

        return redirect()->route('tim.analisa.index', $this->tim()->slug)->with('success', 'Analisa diperbarui.');
    }

    public function destroy(Request $request, $tim, Analisa $analisa): RedirectResponse
    {
        $this->izin(Izin::kelolaKatalog($request->user()));
        $analisa->delete();

        return back()->with('success', 'Analisa dihapus.');
    }

    private function form(Analisa $analisa): View
    {
        return view('tim.katalog.analisa_form', [
            'tim' => $this->tim(),
            'analisa' => $analisa,
            'material' => Material::aktif()->get(['id', 'nama', 'kategori', 'satuan', 'harga']),
            'kategori' => KategoriPekerjaan::opsi(),
            'satuan' => SatuanVolume::opsi(),
        ]);
    }

    private function simpan(Request $request, Analisa $analisa): void
    {
        $data = $request->validate([
            'kode' => ['nullable', 'string', 'max:30'],
            'nama' => ['required', 'string', 'max:200'],
            'kategori' => ['required', Rule::enum(KategoriPekerjaan::class)],
            'satuan' => ['required', 'string', 'max:20'],
            'keterangan' => ['nullable', 'string', 'max:255'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.material_id' => ['required', 'integer'],
            'items.*.koefisien' => ['required', 'numeric', 'gt:0'],
        ], ['items.required' => 'Tambahkan minimal satu material/upah.']);

        $ids = collect($data['items'])->pluck('material_id')->unique();

        if (Material::whereIn('id', $ids)->count() !== $ids->count()) {
            throw ValidationException::withMessages(['items' => 'Ada material yang tidak ditemukan di katalog tim.']);
        }

        DB::transaction(function () use ($analisa, $data) {
            $analisa->fill(collect($data)->except('items')->all())->save();
            $analisa->items()->delete();

            foreach (array_values($data['items']) as $i => $item) {
                $analisa->items()->create([
                    'material_id' => $item['material_id'],
                    'koefisien' => $item['koefisien'],
                    'urutan' => $i,
                ]);
            }
        });
    }
}
