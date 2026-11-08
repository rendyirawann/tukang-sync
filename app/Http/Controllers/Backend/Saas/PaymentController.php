<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backend\Saas;

use App\Enums\StatusPembayaran;
use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\TeamPayment;
use App\Services\Saas\PenyediaTim;
use App\Support\ActivityRecorder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PaymentController extends Controller
{
    public function __construct(private readonly PenyediaTim $penyedia) {}

    public function index(Request $request): View
    {
        $status = (string) $request->query('status', 'menunggu');
        $cari = trim((string) $request->query('q'));

        $bayar = TeamPayment::query()
            ->with(['team', 'plan'])
            ->when($status !== 'semua' && StatusPembayaran::tryFrom($status), fn ($q) => $q->where('status', $status))
            ->when($cari !== '', function ($q) use ($cari) {
                $like = '%'.mb_strtolower($cari).'%';
                $q->where(fn ($w) => $w->whereRaw('LOWER(kode) LIKE ?', [$like])
                    ->orWhereHas('team', fn ($t) => $t->withTrashed()->whereRaw('LOWER(nama) LIKE ?', [$like])->orWhereRaw('LOWER(slug) LIKE ?', [$like])));
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $ringkasan = [
            'menunggu' => TeamPayment::where('status', StatusPembayaran::Menunggu)->count(),
            'bulan_ini' => (int) TeamPayment::where('status', StatusPembayaran::Diterima)
                ->where('dikonfirmasi_pada', '>=', now()->startOfMonth())->sum('nominal'),
            'total' => (int) TeamPayment::where('status', StatusPembayaran::Diterima)->sum('nominal'),
        ];

        return view('backend.saas.bayar.index', compact('bayar', 'status', 'cari', 'ringkasan'));
    }

    public function show(TeamPayment $payment): View
    {
        $payment->load(['team.plan', 'plan', 'bankAccount', 'konfirmator']);

        return view('backend.saas.bayar.show', [
            'bayar' => $payment,
            'pemilik' => $payment->team?->pemilik(),
            'rekening' => BankAccount::aktif()->get(),
        ]);
    }

    /** Menampilkan bukti transfer (disimpan privat). */
    public function bukti(TeamPayment $payment): StreamedResponse
    {
        abort_unless($payment->punyaBukti(), 404);

        return Storage::disk('local')->response($payment->bukti_path, $payment->kode.'.'.pathinfo($payment->bukti_path, PATHINFO_EXTENSION), [
            'Content-Type' => $payment->bukti_mime,
            'Cache-Control' => 'private, no-store',
        ]);
    }

    public function unggahBukti(Request $request, TeamPayment $payment): RedirectResponse
    {
        $request->validate([
            'bukti' => ['nullable', 'required_without:bukti_data', 'file', 'mimes:png,jpg,jpeg,pdf', 'max:5120'],
            'bukti_data' => ['nullable', 'required_without:bukti', 'string'],
        ], ['required_without' => 'Pilih berkas atau tempel (Ctrl+V) gambar bukti transfer.']);

        $this->penyedia->simpanBukti($payment, $request->file('bukti'), $request->input('bukti_data'));

        return back()->with('success', 'Bukti transfer tersimpan.');
    }

    /** ACC: tim aktif, masa langganan berjalan, akun owner bisa login. */
    public function terima(Request $request, TeamPayment $payment): RedirectResponse
    {
        $data = $request->validate([
            'catatan' => ['nullable', 'string', 'max:255'],
            'bank_account_id' => ['nullable', 'exists:bank_accounts,id'],
            'bukti' => ['nullable', 'file', 'mimes:png,jpg,jpeg,pdf', 'max:5120'],
            'bukti_data' => ['nullable', 'string'],
        ]);

        if ($request->hasFile('bukti') || $request->filled('bukti_data')) {
            $this->penyedia->simpanBukti($payment, $request->file('bukti'), $request->input('bukti_data'));
        }

        $this->penyedia->konfirmasi($payment, $request->user(), $data['catatan'] ?? null, $data['bank_account_id'] ?? null);

        $tim = $payment->team->fresh();
        ActivityRecorder::log('saas', 'Menerima pembayaran '.$payment->kode.' tim '.$tim->slug, ['tim' => $tim->slug]);

        return redirect()->route('saas.bayar.show', $payment)
            ->with('success', "Pembayaran diterima. Tim {$tim->slug} aktif sampai {$tim->masaBerlakuLabel()}.");
    }

    public function tolak(Request $request, TeamPayment $payment): RedirectResponse
    {
        $data = $request->validate(['alasan' => ['required', 'string', 'max:255']]);

        $this->penyedia->tolak($payment, $request->user(), $data['alasan']);
        ActivityRecorder::log('saas', 'Menolak pembayaran '.$payment->kode, ['tim' => $payment->team?->slug]);

        return back()->with('success', 'Pembayaran ditolak.');
    }
}
