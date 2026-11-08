<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tim;

use App\Models\Material;
use App\Support\Izin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MaterialController extends TimController
{
    public function index(Request $request): View
    {
        $kategori = (string) $request->query('kategori');
        $cari = trim((string) $request->query('q'));

        $material = Material::query()
            ->when(array_key_exists($kategori, Material::KATEGORI), fn ($q) => $q->where('kategori', $kategori))
            ->when($cari !== '', fn ($q) => $q->whereRaw('LOWER(nama) LIKE ?', ['%'.mb_strtolower($cari).'%']))
            ->orderBy('kategori')->orderBy('nama')
            ->paginate(50)->withQueryString();

        return view('tim.katalog.material', [
            'tim' => $this->tim(), 'material' => $material, 'kategori' => $kategori, 'cari' => $cari,
            'boleh' => Izin::kelolaKatalog($request->user()),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->izin(Izin::kelolaKatalog($request->user()));
        Material::create($this->data($request));

        return back()->with('success', 'Material ditambahkan.');
    }

    public function update(Request $request, $tim, Material $material): RedirectResponse
    {
        $this->izin(Izin::kelolaKatalog($request->user()));
        $material->update($this->data($request));

        return back()->with('success', 'Material diperbarui. RAB lama tetap memakai harga saat dipasang; gunakan "Segarkan harga" di item RAB bila perlu.');
    }

    public function destroy(Request $request, $tim, Material $material): RedirectResponse
    {
        $this->izin(Izin::kelolaKatalog($request->user()));
        $material->delete();

        return back()->with('success', 'Material dihapus dari katalog.');
    }

    private function data(Request $r): array
    {
        return $r->validate([
            'kode' => ['nullable', 'string', 'max:30'],
            'nama' => ['required', 'string', 'max:150'],
            'kategori' => ['required', Rule::in(array_keys(Material::KATEGORI))],
            'satuan' => ['required', 'string', 'max:30'],
            'harga' => ['required', 'numeric', 'min:0'],
            'merek' => ['nullable', 'string', 'max:80'],
            'toko' => ['nullable', 'string', 'max:120'],
            'keterangan' => ['nullable', 'string', 'max:200'],
        ]) + ['aktif' => $r->boolean('aktif', true)];
    }
}
