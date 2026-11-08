<form method="POST" action="{{ $aksi }}" class="row g-3">
    @csrf @if ($u) @method('PUT') @endif
    <div class="{{ $u ? 'col-12' : 'col-md-4' }}"><label class="form-label fs-7">Nama</label><input name="name" value="{{ $u?->name }}" class="form-control form-control-sm" required maxlength="120"></div>
    <div class="{{ $u ? 'col-12' : 'col-md-4' }}"><label class="form-label fs-7">Email</label><input type="email" name="email" value="{{ $u?->email }}" class="form-control form-control-sm" required></div>
    <div class="{{ $u ? 'col-12' : 'col-md-4' }}"><label class="form-label fs-7">Peran</label>
        <select name="peran" class="form-select form-select-sm">@foreach ($peran as $p)<option value="{{ $p->value }}" @selected(($u?->peran?->value ?? 'anggota') === $p->value)>{{ $p->label() }}</option>@endforeach</select></div>
    <div class="{{ $u ? 'col-12' : 'col-md-4' }}"><label class="form-label fs-7">No. WhatsApp</label><input name="no_wa" value="{{ $u?->no_wa }}" class="form-control form-control-sm"></div>
    <div class="{{ $u ? 'col-12' : 'col-md-4' }}"><label class="form-label fs-7">Jabatan</label><input name="jabatan" value="{{ $u?->jabatan }}" class="form-control form-control-sm"></div>
    <div class="{{ $u ? 'col-12' : 'col-md-4' }}"><label class="form-label fs-7">{{ $u ? 'Password baru (kosongkan bila tetap)' : 'Password' }}</label><input type="password" name="password" class="form-control form-control-sm" {{ $u ? '' : 'required' }} autocomplete="new-password"></div>
    @if ($u)
        <div class="col-12"><label class="form-check"><input type="hidden" name="is_active" value="0"><input class="form-check-input" type="checkbox" name="is_active" value="1" @checked($u->is_active)> <span class="form-check-label fs-7">Akun aktif</span></label></div>
    @endif
    <div class="col-12"><button class="btn btn-sm btn-primary">Simpan</button></div>
</form>
