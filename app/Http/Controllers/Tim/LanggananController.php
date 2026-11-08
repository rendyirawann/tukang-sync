<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tim;

use App\Models\AdminContact;
use App\Models\BankAccount;
use App\Models\Plan;
use App\Services\Saas\PenyediaTim;
use App\Support\Izin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LanggananController extends TimController
{
    public function index(Request $request): View
    {
        $tim = $this->tim()->load('plan');

        return view('tim.langganan.index', [
            'tim' => $tim,
            'plans' => Plan::aktif()->get(),
            'riwayat' => $tim->payments()->with('plan')->get(),
            'rekening' => BankAccount::aktif()->get(),
            'boleh' => Izin::kelolaLangganan($request->user()),
        ]);
    }

    public function store(Request $request, PenyediaTim $penyedia): RedirectResponse
    {
        $this->izin(Izin::kelolaLangganan($request->user()));

        $data = $request->validate(['plan_id' => ['required', Rule::exists('plans', 'id')->where('aktif', true)]]);
        $tim = $this->tim();
        $bayar = $penyedia->tagihan($tim, Plan::findOrFail($data['plan_id']))->load('plan');
        $kontak = AdminContact::utama();

        if (! $kontak) {
            return back()->with('success', "Tagihan {$bayar->kode} dibuat. Hubungi admin untuk konfirmasi pembayaran.");
        }

        return redirect()->away($kontak->linkWa(
            StatusController::pesanWa($tim, $bayar, $request->user()->name, 'perpanjangan langganan')
        ));
    }
}
