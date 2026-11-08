@php $bolehLog = \App\Support\Izin::isiLogHarian($user); @endphp
<div class="row g-5">
    @if ($bolehLog)
    <div class="col-xl-4">
        <div class="card shadow-sm"><div class="card-header"><h3 class="card-title fw-bold">Catat hari ini</h3></div>
            <div class="card-body">
                <form method="POST" action="{{ route('tim.proyek.log.store', [$tim->slug, $proyek]) }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-6"><label class="form-label fs-7">Tanggal</label><input type="date" name="tanggal" value="{{ now()->toDateString() }}" class="form-control form-control-sm" required></div>
                        <div class="col-6"><label class="form-label fs-7">Cuaca</label><select name="cuaca" class="form-select form-select-sm"><option value="">-</option>@foreach ($cuaca as $k => $v)<option value="{{ $k }}">{{ $v }}</option>@endforeach</select></div>
                        <div class="col-12"><label class="form-label fs-7">Jumlah pekerja</label><input type="number" min="0" name="jumlah_pekerja" class="form-control form-control-sm"></div>
                        <div class="col-12"><label class="form-label fs-7 required">Pekerjaan hari ini</label><textarea name="uraian" rows="3" class="form-control form-control-sm" required></textarea></div>
                        <div class="col-12"><label class="form-label fs-7">Kendala</label><textarea name="kendala" rows="2" class="form-control form-control-sm"></textarea></div>
                    </div>
                    <button class="btn btn-primary btn-sm mt-4 w-100">Simpan log</button>
                </form>
            </div></div>
    </div>
    @endif
    <div class="{{ $bolehLog ? 'col-xl-8' : 'col-12' }}">
        <div class="card shadow-sm"><div class="card-body">
            @forelse ($log as $l)
                <div class="border-bottom py-4">
                    <div class="d-flex justify-content-between flex-wrap gap-2">
                        <div class="fw-bold">{{ $l->tanggal->translatedFormat('l, d M Y') }}
                            <span class="text-muted fw-normal fs-8">· {{ \App\Models\LogHarian::CUACA[$l->cuaca] ?? '-' }} · {{ $l->jumlah_pekerja ?? '-' }} pekerja · {{ $l->pembuat?->name }}</span></div>
                        @if ($bolehLog && ($user->isPengelola() || $l->created_by === $user->id))
                            <form method="POST" action="{{ route('tim.proyek.log.destroy', [$tim->slug, $proyek, $l]) }}" data-confirm="Hapus log ini?">@csrf @method('DELETE')<button class="btn btn-sm btn-light-danger py-1">Hapus</button></form>
                        @endif
                    </div>
                    <div class="mt-2" style="white-space:pre-line">{{ $l->uraian }}</div>
                    @if ($l->kendala)<div class="mt-2 text-danger fs-7" style="white-space:pre-line">Kendala: {{ $l->kendala }}</div>@endif
                </div>
            @empty
                <div class="text-muted text-center py-8">Belum ada log harian.</div>
            @endforelse
            {{ $log?->links() }}
        </div></div>
    </div>
</div>
