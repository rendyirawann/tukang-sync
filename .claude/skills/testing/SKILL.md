---
name: testing
description: Pola feature/unit test Tukangsync - database tes PostgreSQL, factory tim & peran, seeder, dan skenario wajib (izin, viewer, langganan habis, isolasi lintas tim). Pakai saat menulis atau menjalankan test.
---

# Testing

## Menjalankan

```bash
php artisan test                                   # DB o_saas_test (phpunit.xml)
php artisan test tests/Feature/Api                 # satu folder
DB_DATABASE=o_saas_test_x php artisan test         # DB tes lain (mis. saat beberapa proses paralel)
```
Database tes PostgreSQL harus sudah dibuat. Test memakai `RefreshDatabase`.

## Struktur

| Folder | Isi | Basis |
|---|---|---|
| `tests/Feature/Api` | API mobile, RAB, auth, isolasi | `tests/Feature/Api/ApiTestCase.php` |
| `tests/Feature/Tim` | panel web tim | `tests/Feature/Tim/TimTestCase.php` |
| `tests/Feature/Publik` | beranda, pendaftaran | |
| `tests/Feature/Admin` | panel superadmin | `SaasAdminTest.php` |
| `tests/Feature/Security` | header, dsb. | |
| `tests/Unit` | `TaksasiCalculatorTest`, dll. | |

## Data

- Seeder: `RolePermissionSeeder`, `SuperAdminSeeder`, `SaasSeeder`, `DemoTimSeeder` (tim `DEMO` lengkap dengan RAB).
- Factory: `Team::factory()` (state `menunggu`, `ditangguhkan`, `habis`, `lifetime`),
  `User::factory()->forTeam($tim, 'mandor')`, `->nonaktif()`.
- `UserFactory` mengikuti `TimAktif::get()`; panggil `TimAktif::reset()` sebelum membuat superadmin.
- API: `Sanctum::actingAs($user)`; web: `$this->actingAs($user)`.

## Skenario wajib untuk fitur baru

- [ ] Peran: pengelola boleh, pelaksana sesuai `Izin`, viewer ditolak menulis
- [ ] Langganan habis: GET 200, POST ditolak (API 402 `SUBSCRIPTION_EXPIRED`)
- [ ] Isolasi: tim lain → 404; FK dari tim lain → 422/404
- [ ] Validasi 422 (web: `assertSessionHasErrors`, API: `code` = `VALIDATION_ERROR`)

## Hindari

- Menjalankan `migrate:fresh` pada DB pengembangan (`tukangsync`).
- Test yang bergantung pada urutan ID dari DB pengembangan.
