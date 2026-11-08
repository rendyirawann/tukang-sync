---
name: validation-and-errors
description: Pola validasi dan penanganan error Tukangsync - FormRequest/validate dengan pesan Bahasa Indonesia, error per field di Blade, JSON 422 untuk API, dan halaman error. Pakai saat membuat/mengubah form, endpoint, atau tampilan error.
---

# Validasi & error

## Web (Blade)

1. Validasi di FormRequest atau `$request->validate([...], [pesan], [atribut])`.
2. Gagal → redirect back `withInput()` otomatis. Di view:
   ```blade
   <input name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama') }}">
   @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
   ```
   Ringkasan di atas form: `resources/views/backend/saas/_header.blade.php` (admin) atau pola sama di panel tim.
3. Error bisnis: `throw ValidationException::withMessages(['field' => '...'])` dari Service
   (contoh `app/Services/Saas/KelolaAnggota.php` kuota akun).
4. Flash: `->with('success'|'error'|'warning', '...')` (ditampilkan toastr oleh layout).

## API

- Envelope konsisten dari `bootstrap/app.php` `withExceptions()`:
  `{success:false, message, code:"VALIDATION_ERROR", errors:{field:[...]}}` status 422.
- Error lain: `App\Support\ApiResponse::error($pesan, $status, code: 'KODE')`. Daftar kode: `docs/API.md`.
- Aplikasi mobile menampilkan `message`; jangan ubah bentuk envelope.

## Bahasa

- Pesan & atribut Bahasa Indonesia (`lang/id/validation.php`, `APP_LOCALE=id`).
- Rule `exists`/`unique` pada tabel tim di-scope `team_id`.

## Halaman error

- `resources/views/errors/*.blade.php` mandiri (CSS inline, tanpa query DB/auth).
- 4xx boleh menampilkan pesan `abort(403, '...')`; 5xx tidak menampilkan detail.

## Checklist

- [ ] Setiap form: `@csrf`, `@method` bila perlu, `@error` per field, `old()`
- [ ] Test 422 untuk web (assertSessionHasErrors) dan API (assertStatus(422)->assertJsonPath('code','VALIDATION_ERROR'))

## Hindari

- `dd()`/`dump()` tertinggal; pesan Inggris bawaan untuk atribut domain.
