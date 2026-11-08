---
name: code-standards
description: Konvensi penamaan, arsitektur, format, dan PHPDoc Tukangsync. Pakai saat menulis atau merapikan kode PHP/Blade di project ini, atau saat ragu memilih nama/struktur.
---

# Standar kode

## Arsitektur
Controller → FormRequest/validate → `App\Support\Izin` → Service/Action → Model → View/Resource.

| Benar | Salah |
|---|---|
| `app(RabCalculator::class)->hitungUlang($p)` | hitung volume di controller |
| `if (! Izin::kelolaRab($user)) abort(403);` | `@if(auth()->user()->peran === 'owner')` di Blade |
| `KategoriPekerjaan::from($v)->label()` | string ajaib `'pondasi'` tersebar |
| `DB::transaction(fn () => ...)` saat menulis beberapa tabel | beberapa `save()` lepas |

## Penamaan
- Domain memakai istilah Indonesia yang sudah ada: `Kegiatan` (UI: Proyek), `Pekerjaan`, `Bangunan`,
  `Lantai`, `Ruangan`, `Material`, `Analisa`, method `kelolaRab()`, `hitungUlang()`. Jangan buat sinonim baru.
- Model singular PascalCase; tabel domain lama tetap (`kegiatan`, `lantai`, `analisa`); tabel baru plural snake_case.
- Kolom snake_case; boolean `is_`/`has_` untuk kolom baru (kolom lama seperti `aktif`, `lifetime` dibiarkan).
- Rute: URL kebab-case, nama titik (`tim.proyek.pekerjaan.update`).
- Variabel camelCase.

## PHP
- `declare(strict_types=1);` di berkas baru, type hint parameter & return, constructor property promotion,
  `readonly` untuk dependensi, `match`, early return, method pendek.
- `$fillable` eksplisit; tidak ada `$guarded = []`.
- `config()` di kode; `env()` hanya di `config/`.
- Eager loading untuk relasi di list (`->with([...])`).

## PHPDoc
Bahasa Indonesia, jelaskan MENGAPA:
```php
/**
 * Menerapkan satu paket yang sudah dibayar ke masa langganan.
 *
 * Perpanjangan dihitung dari tanggal berakhir bila masih berlaku, supaya
 * membayar lebih awal tidak menghanguskan sisa hari.
 *
 * @return array{0: Carbon, 1: Carbon|null}
 */
```
(contoh nyata: `Team::terapkanPaket()` di `app/Models/Team.php`)

## Format & commit
- `./vendor/bin/pint` (preset laravel) — format massal di commit terpisah `style: apply pint`.
- PHPStan (bila `phpstan.neon` ada).
- Conventional Commits: `feat:`, `fix:`, `refactor:`, `docs:`, `style:`, `test:`, `chore:`.

## Hindari
- `php -r`/`sed` regex berisi backslash untuk mengedit berkas (pernah mengosongkan controller). Pakai tool Edit.
- Mengubah perilaku saat merapikan kode lama.
