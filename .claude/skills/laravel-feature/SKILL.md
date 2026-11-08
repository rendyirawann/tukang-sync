---
name: laravel-feature
description: Dipakai saat membuat modul/CRUD baru di Tukangsync end-to-end (migration, model ber-tim, enum, factory, request, izin, service, controller, rute panel tim/admin/API, view, menu, test, docs). Pakai setiap kali menambah entitas atau layar baru.
---

# Membuat fitur baru

## Tujuan
Fitur baru yang aman untuk multi-tenant, konsisten dengan pola yang ada, dan teruji.

## Langkah
1. **Tentukan pintu**: panel tim (`routes/tim_panel.php`, controller `app/Http/Controllers/Tim/`),
   superadmin (`routes/admin_saas.php`, `app/Http/Controllers/Backend/Saas/`), atau API
   (`routes/api.php`, `app/Http/Controllers/Api/`). Sering dua sekaligus (web tim + API).
2. **Migration** di `database/migrations/` dengan `team_id`:
   ```php
   $table->foreignId('team_id')->constrained('teams')->cascadeOnDelete();
   $table->foreignId('kegiatan_id')->constrained('kegiatan')->cascadeOnDelete();
   $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
   ```
   Index kolom filter. Contoh: `2026_11_08_000300_create_rab_tables.php`.
3. **Model** dengan `use MilikTim` (`app/Models/Concerns/MilikTim.php`), `$fillable` eksplisit,
   `casts()`, relasi. Contoh: `app/Models/LogHarian.php`, `app/Models/Pekerjaan.php`.
4. **Enum** untuk status/tipe di `app/Enums/` dengan `label()` dan `opsi()` (lihat `KategoriPekerjaan`).
5. **Factory** di `database/factories/` (set `team_id`).
6. **Izin**: tambah method di `app/Support/Izin.php` dan masukkan ke `ringkasan()` bila aplikasi mobile butuh.
7. **Validasi**: FormRequest (API: `app/Http/Requests/Rab/*`) atau `$request->validate()`; unique/exists
   di-scope `->where('team_id', TimAktif::id())`; validasi induk-anak (lihat `StrukturController`).
8. **Service** untuk logika bisnis (`app/Services/...`), `DB::transaction()` untuk multi-tabel.
9. **Controller** tipis. Web tim: abort 403 bila `! Izin::...`; API: `ApiResponse::error(..., 403, code: 'FORBIDDEN')`.
   API pakai JsonResource (`app/Http/Resources/Rab/*`) + atribut OpenAPI, lalu `php artisan l5-swagger:generate`.
10. **Rute**: URL kebab-case, nama titik (`tim.proyek.log.store`). Rute `/{tim}` tetap di dalam `routes/tim_panel.php`.
11. **View**: panel tim extend `tim.layout.app`; admin extend `backend.layout.app`. Tombol ubah disembunyikan
    via `Izin`. Form: `@csrf`, `@method`, `@error`.
12. **Menu**: sidebar tim `resources/views/tim/layout/app.blade.php`; admin `backend/layout/menu.blade.php` + `sidebar.blade.php`.
13. **Test** (lihat skill `testing`): izin per peran, viewer, langganan habis, isolasi lintas tim.
14. **Docs**: `docs/DATABASE.md` (tabel/ERD), `docs/API.md`/`API-RAB.md`, `docs/CHANGELOG.md`.

## Checklist
- [ ] Model tim memakai `MilikTim`; anak divalidasi milik induk yang sama
- [ ] Izin di `Izin.php`, bukan if-else di Blade
- [ ] Tidak ada model ber-ID integer sebagai subject `ActivityRecorder::log()`
- [ ] Test lulus (`php artisan test`), Swagger tergenerate
- [ ] Teks UI Bahasa Indonesia

## Hindari
- Query `User::` tanpa `anggotaTim()`.
- `Rule::unique('tabel')` tanpa scope tim.
- Logika hitung di view/controller (pindah ke Service, mis. `RabCalculator`).
