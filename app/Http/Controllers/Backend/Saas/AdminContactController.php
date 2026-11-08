<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backend\Saas;

use App\Http\Controllers\Controller;
use App\Models\AdminContact;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/** Nomor WhatsApp admin yang dihubungi pendaftar untuk konfirmasi. */
class AdminContactController extends Controller
{
    public function index(): View
    {
        return view('backend.saas.kontak.index', [
            'kontak' => AdminContact::orderByDesc('utama')->orderBy('urutan')->orderBy('id')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->simpan($request, new AdminContact);

        return back()->with('success', 'Kontak ditambahkan.');
    }

    public function update(Request $request, AdminContact $adminContact): RedirectResponse
    {
        $this->simpan($request, $adminContact);

        return back()->with('success', 'Kontak diperbarui.');
    }

    public function destroy(AdminContact $adminContact): RedirectResponse
    {
        $adminContact->delete();

        return back()->with('success', 'Kontak dihapus.');
    }

    private function simpan(Request $request, AdminContact $kontak): void
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:80'],
            'no_wa' => ['required', 'string', 'max:20', 'regex:/^[0-9+\s-]{8,20}$/'],
            'keterangan' => ['nullable', 'string', 'max:200'],
            'urutan' => ['nullable', 'integer', 'min:0'],
        ], ['no_wa.regex' => 'Nomor WhatsApp hanya boleh berisi angka, spasi, + atau -.']);

        DB::transaction(function () use ($request, $kontak, $data) {
            $utama = $request->boolean('utama');

            // Hanya satu kontak utama: dialah tujuan tombol konfirmasi.
            if ($utama) {
                AdminContact::where('id', '!=', $kontak->id ?? 0)->update(['utama' => false]);
            }

            $kontak->fill($data + [
                'aktif' => $request->boolean('aktif'),
                'utama' => $utama,
                'urutan' => (int) $request->input('urutan', 0),
            ])->save();
        });
    }
}
