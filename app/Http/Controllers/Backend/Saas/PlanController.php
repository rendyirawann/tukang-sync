<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backend\Saas;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PlanController extends Controller
{
    public function index(): View
    {
        return view('backend.saas.paket.index', [
            'plans' => Plan::withCount('teams')->orderBy('urutan')->orderBy('harga')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Plan::create($this->data($request));

        return back()->with('success', 'Paket ditambahkan.');
    }

    public function update(Request $request, Plan $plan): RedirectResponse
    {
        $plan->update($this->data($request, $plan));

        return back()->with('success', 'Paket diperbarui. Harga baru berlaku untuk tagihan berikutnya.');
    }

    public function destroy(Plan $plan): RedirectResponse
    {
        if ($plan->teams()->exists()) {
            $plan->update(['aktif' => false]);

            return back()->with('warning', 'Paket masih dipakai tim, jadi hanya dinonaktifkan.');
        }

        $plan->delete();

        return back()->with('success', 'Paket dihapus.');
    }

    /** @return array<string, mixed> */
    private function data(Request $request, ?Plan $plan = null): array
    {
        $data = $request->validate([
            'kode' => ['required', 'alpha_dash', 'max:30', Rule::unique('plans', 'kode')->ignore($plan?->id)],
            'nama' => ['required', 'string', 'max:80'],
            'harga' => ['required', 'integer', 'min:0'],
            'jenis' => ['required', Rule::in(['berkala', 'lifetime'])],
            'durasi_bulan' => ['nullable', 'required_if:jenis,berkala', 'integer', 'min:1', 'max:60'],
            'max_users' => ['required', 'integer', 'min:1', 'max:50'],
            'deskripsi' => ['nullable', 'string', 'max:255'],
            'fitur' => ['nullable', 'string', 'max:2000'],
            'urutan' => ['nullable', 'integer', 'min:0'],
        ]);

        return [
            'kode' => $data['kode'],
            'nama' => $data['nama'],
            'harga' => $data['harga'],
            'durasi_bulan' => $data['jenis'] === 'lifetime' ? null : (int) $data['durasi_bulan'],
            'max_users' => $data['max_users'],
            'deskripsi' => $data['deskripsi'] ?? null,
            // Satu fitur per baris di textarea.
            'fitur' => array_values(array_filter(array_map('trim', preg_split('/\R/', (string) ($data['fitur'] ?? ''))))),
            'urutan' => $data['urutan'] ?? 0,
            'aktif' => $request->boolean('aktif'),
        ];
    }
}
