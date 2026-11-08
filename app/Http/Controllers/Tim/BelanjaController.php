<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tim;

use App\Models\BahanBakuItem;
use App\Models\Kegiatan;
use App\Models\Material;
use App\Models\Pekerjaan;
use App\Support\Izin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/** Belanja bahan (bahan baku) per proyek, tertaut opsional ke katalog & item RAB. */
class BelanjaController extends TimController
{
    public function store(Request $request, $tim, Kegiatan $kegiatan): RedirectResponse
    {
        $this->izin(Izin::kelolaBahanBaku($request->user()));

        $data = $request->validate([
            'material_id' => ['nullable', 'integer'],
            'pekerjaan_id' => ['nullable', 'integer'],
            'nama' => ['required_without:material_id', 'nullable', 'string', 'max:150'],
            'satuan' => ['nullable', 'string', 'max:30'],
            'qty' => ['required', 'numeric', 'gt:0'],
            'harga_satuan' => ['required', 'integer', 'min:0'],
            'tanggal_beli' => ['nullable', 'date'],
            'toko' => ['nullable', 'string', 'max:120'],
            'keterangan' => ['nullable', 'string', 'max:1000'],
        ]);

        $material = ! empty($data['material_id']) ? Material::find($data['material_id']) : null;
        $pekerjaan = ! empty($data['pekerjaan_id']) ? Pekerjaan::find($data['pekerjaan_id']) : null;

        if ((! empty($data['material_id']) && ! $material) || (! empty($data['pekerjaan_id']) && $pekerjaan?->kegiatan_id !== $kegiatan->id)) {
            throw ValidationException::withMessages(['material_id' => 'Material atau item RAB tidak valid.']);
        }

        $item = new BahanBakuItem([
            'kegiatan_id' => $kegiatan->id,
            'nama' => $data['nama'] ?: $material?->nama,
            'satuan' => $data['satuan'] ?: $material?->satuan,
            'qty' => $data['qty'],
            'harga_satuan' => $data['harga_satuan'],
            'tanggal_beli' => $data['tanggal_beli'] ?? now()->toDateString(),
            'toko' => $data['toko'] ?? null,
            'keterangan' => $data['keterangan'] ?? null,
            'created_by' => $request->user()->id,
        ]);
        $item->forceFill(['material_id' => $material?->id, 'pekerjaan_id' => $pekerjaan?->id]);
        $item->save();

        return redirect()->route('tim.proyek.show', [$this->tim()->slug, $kegiatan, 'tab' => 'belanja'])->with('success', 'Belanja dicatat.');
    }

    public function destroy(Request $request, $tim, Kegiatan $kegiatan, BahanBakuItem $bahanBaku): RedirectResponse
    {
        $this->izin(Izin::kelolaBahanBaku($request->user()));
        $this->milik($bahanBaku->kegiatan_id === $kegiatan->id);
        $bahanBaku->delete();

        return redirect()->route('tim.proyek.show', [$this->tim()->slug, $kegiatan, 'tab' => 'belanja'])->with('success', 'Belanja dihapus.');
    }
}
