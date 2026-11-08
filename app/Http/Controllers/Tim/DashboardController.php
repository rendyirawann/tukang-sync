<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tim;

use App\Models\BahanBakuItem;
use App\Models\Kegiatan;
use App\Models\LogHarian;
use App\Models\Pekerjaan;
use Illuminate\View\View;

class DashboardController extends TimController
{
    public function index(): View
    {
        $tim = $this->tim();

        $perStatus = Kegiatan::query()->selectRaw('status, COUNT(*) AS jml')->groupBy('status')->pluck('jml', 'status');

        $rab = Pekerjaan::query()->selectRaw('kegiatan_id, SUM(total) AS total, SUM(total * progres) AS bobot')
            ->groupBy('kegiatan_id')->get()->keyBy('kegiatan_id');

        $totalRab = (int) $rab->sum('total');
        $progres = $totalRab > 0 ? round($rab->sum('bobot') / $totalRab, 1) : 0;

        $proyek = Kegiatan::query()->latest()->take(6)->get()->each(function (Kegiatan $k) use ($rab) {
            $r = $rab->get($k->id);
            $k->setAttribute('rab_total', (int) ($r->total ?? 0));
            $k->setAttribute('rab_progres', $r && $r->total > 0 ? round($r->bobot / $r->total, 1) : 0);
        });

        return view('tim.dashboard', [
            'tim' => $tim,
            'perStatus' => $perStatus,
            'jumlahProyek' => (int) $perStatus->sum(),
            'totalRab' => $totalRab,
            'progres' => $progres,
            'belanjaBulanIni' => (int) BahanBakuItem::query()->where('tanggal_beli', '>=', now()->startOfMonth())->sum('subtotal'),
            'proyek' => $proyek,
            'log' => LogHarian::query()->with(['kegiatan:id,nama', 'pembuat:id,name'])->latest('tanggal')->latest('id')->take(5)->get(),
        ]);
    }
}
