<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tim;

use App\Enums\StatusPembayaran;
use App\Http\Controllers\Controller;
use App\Models\AdminContact;
use App\Models\BankAccount;
use App\Models\Team;
use App\Models\TeamPayment;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Halaman status pendaftaran/pembayaran: domain/{kode}/status. */
class StatusController extends Controller
{
    public function show(Request $request, Team $tim): View
    {
        $bayar = $tim->payments()->with('plan')->where('status', StatusPembayaran::Menunggu)->first();
        $kontak = AdminContact::utama();
        $pemilik = $tim->pemilik();

        return view('tim.status', [
            'tim' => $tim,
            'bayar' => $bayar,
            'rekening' => BankAccount::aktif()->get(),
            'kontak' => $kontak,
            'linkWa' => $bayar && $kontak ? $kontak->linkWa(self::pesanWa($tim, $bayar, $pemilik?->name)) : null,
            'baru' => $request->boolean('baru'),
        ]);
    }

    public static function pesanWa(Team $tim, TeamPayment $bayar, ?string $nama, string $judul = 'konfirmasi pembayaran'): string
    {
        return "Halo admin, saya ingin {$judul} Transaksi Pekerjaan.\n"
            ."Kode tim: {$tim->slug}\n"
            ."Invoice: {$bayar->kode}\n"
            .'Paket: '.($bayar->plan?->nama ?? '-')."\n"
            .'Nominal: '.$bayar->nominalLabel()."\n"
            .'Nama: '.($nama ?? '-')."\n"
            ."Tim: {$tim->nama}\n"
            .'Bukti transfer saya lampirkan.';
    }
}
