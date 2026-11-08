<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backend\Saas;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Rekening tujuan transfer yang tampil di formulir pendaftaran. */
class BankAccountController extends Controller
{
    public function index(): View
    {
        return view('backend.saas.rekening.index', [
            'rekening' => BankAccount::orderBy('urutan')->orderBy('id')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        BankAccount::create($this->data($request));

        return back()->with('success', 'Rekening ditambahkan.');
    }

    public function update(Request $request, BankAccount $bankAccount): RedirectResponse
    {
        $bankAccount->update($this->data($request));

        return back()->with('success', 'Rekening diperbarui.');
    }

    public function destroy(BankAccount $bankAccount): RedirectResponse
    {
        $bankAccount->delete();

        return back()->with('success', 'Rekening dihapus.');
    }

    /** @return array<string, mixed> */
    private function data(Request $request): array
    {
        return $request->validate([
            'bank' => ['required', 'string', 'max:60'],
            'nomor' => ['required', 'string', 'max:40'],
            'atas_nama' => ['required', 'string', 'max:120'],
            'keterangan' => ['nullable', 'string', 'max:200'],
            'urutan' => ['nullable', 'integer', 'min:0'],
        ]) + ['aktif' => $request->boolean('aktif'), 'urutan' => (int) $request->input('urutan', 0)];
    }
}
