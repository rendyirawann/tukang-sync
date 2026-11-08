<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backend\Saas;

use App\Enums\Peran;
use App\Enums\StatusPembayaran;
use App\Enums\StatusTim;
use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\Plan;
use App\Models\Team;
use App\Models\User;
use App\Services\Saas\PenyediaTim;
use App\Support\ActivityRecorder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class TeamController extends Controller
{
    public function __construct(private readonly PenyediaTim $penyedia) {}

    public function index(Request $request): View
    {
        $cari = trim((string) $request->query('q'));
        $status = (string) $request->query('status');

        $tim = Team::query()
            ->with('plan')
            ->withCount('users')
            ->when($status === 'habis', fn ($q) => $q->where('status', StatusTim::Aktif)
                ->where('lifetime', false)->where('aktif_sampai', '<', now()))
            ->when(in_array($status, array_column(StatusTim::cases(), 'value'), true), fn ($q) => $q->where('status', $status))
            ->when($cari !== '', function ($q) use ($cari) {
                $like = '%'.mb_strtolower($cari).'%';
                $q->where(fn ($w) => $w->whereRaw('LOWER(nama) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(slug) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(email) LIKE ?', [$like])
                    ->orWhere('no_wa', 'like', '%'.$cari.'%'));
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $ringkasan = [
            'total' => Team::count(),
            'aktif' => Team::where('status', StatusTim::Aktif)->count(),
            'menunggu' => Team::where('status', StatusTim::Menunggu)->count(),
            'habis' => Team::where('status', StatusTim::Aktif)->where('lifetime', false)->where('aktif_sampai', '<', now())->count(),
        ];

        $filter = ['' => 'Semua', 'aktif' => 'Aktif', 'menunggu' => 'Menunggu bayar', 'habis' => 'Masa habis', 'ditangguhkan' => 'Ditangguhkan'];

        return view('backend.saas.tim.index', compact('tim', 'ringkasan', 'cari', 'status', 'filter'));
    }

    public function create(): View
    {
        return view('backend.saas.tim.create', [
            'plans' => Plan::aktif()->get(),
            'rekening' => BankAccount::aktif()->get(),
        ]);
    }

    /**
     * Superadmin mendaftarkan tim atas nama pelanggan, memilihkan paket, dan
     * (bila pembayaran sudah diterima) langsung mengaktifkannya.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama_tim' => ['required', 'string', 'max:120'],
            'kota' => ['nullable', 'string', 'max:80'],
            'alamat' => ['nullable', 'string', 'max:255'],
            'nama' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:120', Rule::unique('users', 'email')],
            'no_wa' => ['required', 'string', 'max:20'],
            'password' => ['required', 'string', Password::min(8)->letters()->numbers()],
            'peran' => ['required', Rule::in(array_map(fn ($p) => $p->value, Peran::pendaftar()))],
            'plan_id' => ['required', 'exists:plans,id'],
            'langsung_aktif' => ['nullable', 'boolean'],
            'bank_account_id' => ['nullable', 'exists:bank_accounts,id'],
            'catatan' => ['nullable', 'string', 'max:255'],
            'bukti' => ['nullable', 'file', 'mimes:png,jpg,jpeg,pdf', 'max:5120'],
            'bukti_data' => ['nullable', 'string'],
        ]);

        $bayar = DB::transaction(function () use ($request, $data) {
            $bayar = $this->penyedia->daftar($data, Plan::findOrFail($data['plan_id']));

            if ($request->hasFile('bukti') || $request->filled('bukti_data')) {
                $this->penyedia->simpanBukti($bayar, $request->file('bukti'), $request->input('bukti_data'));
            }

            if ($request->boolean('langsung_aktif')) {
                $this->penyedia->konfirmasi($bayar, $request->user(), $data['catatan'] ?? null, $data['bank_account_id'] ?? null);
            }

            return $bayar;
        });

        $tim = $bayar->team;
        ActivityRecorder::log('saas', 'Mendaftarkan tim '.$tim->nama.' ('.$tim->slug.')', ['tim' => $tim->slug]);

        return redirect()->route('saas.tim.show', $tim->slug)
            ->with('success', $request->boolean('langsung_aktif')
                ? 'Tim dibuat dan langsung aktif. Kode tim: '.$tim->slug
                : 'Tim dibuat, menunggu konfirmasi pembayaran. Kode tim: '.$tim->slug);
    }

    public function show(Team $team): View
    {
        $team->load(['plan', 'payments.plan', 'payments.konfirmator']);

        return view('backend.saas.tim.show', [
            'team' => $team,
            'anggota' => User::anggotaTim($team->id)
                ->orderByRaw("CASE peran WHEN 'owner' THEN 0 WHEN 'kontraktor' THEN 1 ELSE 2 END")
                ->orderBy('created_at')
                ->get(),
            'plans' => Plan::aktif()->get(),
            'rekening' => BankAccount::aktif()->get(),
        ]);
    }

    public function edit(Team $team): View
    {
        return view('backend.saas.tim.edit', ['team' => $team, 'plans' => Plan::orderBy('urutan')->get()]);
    }

    public function update(Request $request, Team $team): RedirectResponse
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:120'],
            'kota' => ['nullable', 'string', 'max:80'],
            'alamat' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:120'],
            'no_wa' => ['nullable', 'string', 'max:20'],
            'plan_id' => ['nullable', 'exists:plans,id'],
            'max_users' => ['required', 'integer', 'min:1', 'max:50'],
            'lifetime' => ['nullable', 'boolean'],
            'aktif_sampai' => ['nullable', 'date'],
            'catatan_admin' => ['nullable', 'string', 'max:2000'],
        ]);

        $data['lifetime'] = $request->boolean('lifetime');
        $jumlahAkun = $team->users()->count();

        if ($data['max_users'] < $jumlahAkun) {
            return back()->withInput()->with('error', "Batas akun tidak boleh lebih kecil dari jumlah akun yang sudah ada ({$jumlahAkun}).");
        }

        $team->update($data);
        ActivityRecorder::log('saas', 'Mengubah data tim '.$team->slug, ['tim' => $team->slug]);

        return redirect()->route('saas.tim.show', $team->slug)->with('success', 'Data tim diperbarui.');
    }

    /** Menangguhkan atau mengaktifkan kembali tim. */
    public function status(Request $request, Team $team): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in([StatusTim::Aktif->value, StatusTim::Ditangguhkan->value])],
        ]);

        $team->update(['status' => $data['status']]);
        $aktif = $data['status'] === StatusTim::Aktif->value;

        if (! $aktif) {
            // Token API dicabut supaya aplikasi mobile ikut tertutup saat itu juga.
            User::anggotaTim($team->id)->get()->each(fn (User $u) => $u->tokens()->delete());
        }

        ActivityRecorder::log('saas', ($aktif ? 'Mengaktifkan' : 'Menangguhkan').' tim '.$team->slug, ['tim' => $team->slug]);

        return back()->with('success', $aktif ? 'Tim diaktifkan kembali.' : 'Tim ditangguhkan; seluruh sesi anggotanya dicabut.');
    }

    /** Mencatat pembayaran (perpanjangan / ganti paket) dari panel superadmin. */
    public function tambahPembayaran(Request $request, Team $team): RedirectResponse
    {
        $data = $request->validate([
            'plan_id' => ['required', 'exists:plans,id'],
            'bank_account_id' => ['nullable', 'exists:bank_accounts,id'],
            'catatan' => ['nullable', 'string', 'max:255'],
            'langsung_terima' => ['nullable', 'boolean'],
            'bukti' => ['nullable', 'file', 'mimes:png,jpg,jpeg,pdf', 'max:5120'],
            'bukti_data' => ['nullable', 'string'],
        ]);

        $bayar = DB::transaction(function () use ($request, $team, $data) {
            $plan = Plan::findOrFail($data['plan_id']);

            $bayar = $team->payments()->create([
                'plan_id' => $plan->id,
                'jenis' => $team->diaktifkan_pada ? 'perpanjangan' : 'pendaftaran',
                'nominal' => $plan->harga,
                'status' => StatusPembayaran::Menunggu,
                'bank_account_id' => $data['bank_account_id'] ?? null,
                'catatan' => $data['catatan'] ?? null,
            ]);

            if ($request->hasFile('bukti') || $request->filled('bukti_data')) {
                $this->penyedia->simpanBukti($bayar, $request->file('bukti'), $request->input('bukti_data'));
            }

            if ($request->boolean('langsung_terima')) {
                $this->penyedia->konfirmasi($bayar, $request->user());
            }

            return $bayar;
        });

        ActivityRecorder::log('saas', 'Mencatat pembayaran '.$bayar->kode.' untuk tim '.$team->slug, ['tim' => $team->slug]);

        return back()->with('success', $request->boolean('langsung_terima')
            ? 'Pembayaran dicatat dan diterima. Masa aktif: '.$team->fresh()->masaBerlakuLabel()
            : 'Pembayaran dicatat, menunggu konfirmasi.');
    }

    public function destroy(Team $team): RedirectResponse
    {
        User::anggotaTim($team->id)->get()->each(function (User $u) {
            $u->tokens()->delete();
            $u->update(['is_active' => false]);
        });

        $team->delete();
        ActivityRecorder::log('saas', 'Menghapus tim '.$team->slug, ['tim' => $team->slug]);

        return redirect()->route('saas.tim.index')->with('success', 'Tim '.$team->nama.' dihapus.');
    }
}
