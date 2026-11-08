<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\AdminContact;
use App\Models\BankAccount;
use App\Models\Plan;
use App\Models\Setting;
use Illuminate\Database\Seeder;

/**
 * Paket langganan dan data tampilan formulir pendaftaran.
 *
 * Rekening dan nomor WhatsApp di sini hanya CONTOH: ganti dari panel
 * superadmin (Langganan › Rekening & Kontak WA) sebelum dipakai pelanggan.
 */
class SaasSeeder extends Seeder
{
    public function run(): void
    {
        Plan::updateOrCreate(['kode' => 'bulanan'], [
            'nama' => 'Bulanan',
            'harga' => 49000,
            'durasi_bulan' => 1,
            'max_users' => 4,
            'deskripsi' => 'Bayar per bulan, bisa berhenti kapan saja.',
            'fitur' => ['Proyek & RAB tanpa batas', 'Maksimal 4 akun tim', 'Aplikasi Android + web', 'Laporan PDF'],
            'aktif' => true,
            'urutan' => 1,
        ]);

        Plan::updateOrCreate(['kode' => 'lifetime'], [
            'nama' => 'Lifetime',
            'harga' => 99000,
            'durasi_bulan' => null,
            'max_users' => 4,
            'deskripsi' => 'Sekali bayar, dipakai selamanya.',
            'fitur' => ['Semua fitur paket Bulanan', 'Tanpa biaya bulanan', 'Pembaruan aplikasi gratis'],
            'aktif' => true,
            'urutan' => 2,
        ]);

        if (BankAccount::count() === 0) {
            BankAccount::create([
                'bank' => 'BCA',
                'nomor' => '0000000000',
                'atas_nama' => 'GANTI DI PANEL SUPERADMIN',
                'keterangan' => 'Contoh, ubah di Langganan › Rekening',
                'aktif' => true,
            ]);
        }

        if (AdminContact::count() === 0) {
            AdminContact::create([
                'nama' => 'Admin',
                'no_wa' => '081234567890',
                'keterangan' => 'Contoh, ubah di Langganan › Kontak WhatsApp',
                'aktif' => true,
                'utama' => true,
            ]);
        }

        Setting::set('site_name', 'Tukangsync');
    }
}
