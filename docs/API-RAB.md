# Kontrak API RAB

Semua endpoint di bawah berada di dalam grup `auth:sanctum` + `tim.api`
(data otomatis milik tim akun yang login), memakai envelope standar o-api:

```json
{ "success": true, "message": "…", "data": … }
{ "success": false, "message": "…", "code": "VALIDATION_ERROR", "errors": {…} }
```

Angka: uang = integer rupiah; dimensi/volume/qty/koefisien = number (float).
ID RAB = integer. ID akun = string UUID.

Izin (lihat `App\Support\Izin`):

| Aksi | Izin |
|---|---|
| Membaca semua endpoint | semua anggota tim (termasuk viewer) |
| Struktur, pekerjaan, analisa pekerjaan, kebutuhan | `kelolaRab` (owner, kontraktor) |
| Katalog material & analisa | `kelolaKatalog` (owner, kontraktor) |
| `PATCH /pekerjaan/{id}/progres` | `isiProgres` (+ mandor, anggota) |
| Log harian | `isiLogHarian` (+ mandor, anggota) |

Tolak izin -> `403 FORBIDDEN`. Data milik tim lain -> `404`.

---

## Referensi

`GET /api/referensi/rab`

```json
{
  "kategori_pekerjaan": [{"value": "pondasi", "label": "Pekerjaan Pondasi"}],
  "komponen": [{"value": "pintu", "label": "Pintu", "satuan": "unit", "kategori": "kusen"}],
  "satuan": [{"value": "m2", "label": "m² (luas)", "simbol": "m²", "dimensi": ["panjang","lebar"], "otomatis": true}],
  "jenis_bangunan": [{"value": "rumah", "label": "Rumah Tinggal"}],
  "kategori_material": [{"value": "material", "label": "Material"}],
  "cuaca": [{"value": "cerah", "label": "Cerah"}]
}
```

## Ringkasan + struktur satu proyek

`GET /api/kegiatan/{kegiatan}/rab`

```json
{
  "ringkasan": {
    "total_rab": 96540808, "jumlah_item": 13, "progres": 44.3, "nilai_progres": 42746000,
    "realisasi_belanja": 0, "pagu": 450000000,
    "per_kategori": [{"kategori": "pondasi", "label": "Pekerjaan Pondasi", "jumlah_item": 1, "total": 24250752, "persen": 25.1}],
    "per_lokasi":   [{"lokasi": "Rumah Utama › Lantai 1", "jumlah_item": 8, "total": 1, "progres": 50.0}]
  },
  "bangunan": [ Bangunan ]
}
```

**Bangunan**
```json
{ "id": 1, "nama": "Rumah Utama", "jenis": "rumah", "jenis_label": "Rumah Tinggal",
  "luas_tanah": 120.0, "luas_bangunan": 140.0, "keterangan": null, "total": 96540808,
  "lantai": [ Lantai ] }
```

**Lantai**
```json
{ "id": 1, "bangunan_id": 1, "nama": "Lantai 1", "nomor": 1, "luas": 80.0, "tinggi": 3.5,
  "keterangan": null, "total": 61000000, "ruangan": [ Ruangan ] }
```

**Ruangan**
```json
{ "id": 1, "lantai_id": 1, "nama": "Dapur", "panjang": 3.0, "lebar": 3.0, "tinggi": 3.5,
  "luas_lantai": 9.0, "luas_dinding": 42.0, "keterangan": null, "total": 2496150 }
```

## Struktur (CRUD)

| Metode | Path | Body |
|---|---|---|
| POST | `/api/kegiatan/{kegiatan}/bangunan` | nama*, jenis, luas_tanah, luas_bangunan, keterangan |
| PUT | `/api/bangunan/{bangunan}` | sama |
| DELETE | `/api/bangunan/{bangunan}` | – (ikut menghapus lantai, ruangan, pekerjaannya) |
| POST | `/api/bangunan/{bangunan}/lantai` | nama*, nomor*, luas, tinggi, keterangan |
| PUT/DELETE | `/api/lantai/{lantai}` | sama |
| POST | `/api/lantai/{lantai}/ruangan` | nama*, panjang, lebar, tinggi, keterangan |
| PUT/DELETE | `/api/ruangan/{ruangan}` | sama |

Respons POST/PUT = objek yang dibuat/diubah (tanpa anak).

## Pekerjaan (item RAB)

`GET /api/kegiatan/{kegiatan}/pekerjaan?bangunan_id=&lantai_id=&ruangan_id=&kategori=`
-> array `Pekerjaan` (tanpa `kebutuhan`), urut `urutan, id`.

`GET /api/pekerjaan/{pekerjaan}` -> `Pekerjaan` lengkap dengan `kebutuhan`.

**Pekerjaan**
```json
{
  "id": 5, "kegiatan_id": 1, "bangunan_id": 1, "lantai_id": 1, "ruangan_id": 2,
  "lokasi": "Rumah Utama › Lantai 1 › Ruang Tamu",
  "kategori": "kusen", "kategori_label": "Pekerjaan Kusen, Pintu & Jendela",
  "komponen": "pintu", "komponen_label": "Pintu",
  "nama": "Pintu utama panel kayu",
  "jumlah": 2.0, "panjang": null, "lebar": null, "tinggi": null,
  "satuan": "unit", "satuan_simbol": "unit",
  "volume": 2.0, "volume_manual": false,
  "harga_satuan": 2310000.0, "total": 4620000,
  "progres": 0, "nilai_progres": 0,
  "analisa_id": 7, "analisa_nama": "Pasang pintu panel kayu lengkap",
  "rencana_mulai": null, "rencana_selesai": null, "keterangan": null,
  "kebutuhan": [ Kebutuhan ]
}
```

**Kebutuhan**
```json
{ "id": 1, "material_id": 28, "nama": "Engsel pintu", "kategori": "material", "satuan": "bh",
  "koefisien": 3.0, "qty": 6.0, "qty_manual": false, "harga_satuan": 25000.0, "subtotal": 150000 }
```

| Metode | Path | Body / catatan |
|---|---|---|
| POST | `/api/kegiatan/{kegiatan}/pekerjaan` | kategori*, komponen, nama*, bangunan_id, lantai_id, ruangan_id, jumlah*, panjang, lebar, tinggi, satuan*, volume (wajib bila volume_manual), volume_manual, harga_satuan, analisa_id (bila diisi: kebutuhan dibuat dari analisa), progres, rencana_mulai, rencana_selesai, keterangan |
| PUT | `/api/pekerjaan/{pekerjaan}` | sama; mengganti analisa_id menerapkan ulang analisa |
| DELETE | `/api/pekerjaan/{pekerjaan}` | – |
| PATCH | `/api/pekerjaan/{pekerjaan}/progres` | progres* (0–100) |
| POST | `/api/pekerjaan/{pekerjaan}/analisa` | analisa_id* -> terapkan ulang analisa |
| POST | `/api/pekerjaan/{pekerjaan}/segarkan-harga` | – -> ambil ulang harga katalog |
| POST | `/api/pekerjaan/{pekerjaan}/kebutuhan` | material_id atau (nama*, satuan*, kategori, harga_satuan*), koefisien atau qty |
| PUT/DELETE | `/api/kebutuhan/{kebutuhan}` | koefisien, qty (qty diisi -> qty_manual=true), harga_satuan |

Setiap perubahan pekerjaan/kebutuhan menghitung ulang lewat
`App\Services\Rab\RabCalculator::hitungUlang()`; respons berisi `Pekerjaan`
terbaru lengkap dengan `kebutuhan`.

Validasi lokasi: lantai_id harus milik bangunan_id, ruangan_id milik lantai_id,
bangunan_id milik kegiatan yang sama.

## Rekap material proyek

`GET /api/kegiatan/{kegiatan}/rekap-material`
-> array dari `RabCalculator::rekapMaterial()`:

```json
[{ "material_id": 14, "nama": "Bata merah", "kategori": "material", "satuan": "bh",
   "qty": 6811.0, "subtotal": 6129900, "qty_realisasi": 0.0, "biaya_realisasi": 0, "selisih": 6129900 }]
```

## Katalog material

| Metode | Path | Catatan |
|---|---|---|
| GET | `/api/material?kategori=&search=&aktif=1` | array `Material` (tanpa paginasi) |
| POST | `/api/material` | kode, nama*, kategori* (material/upah/alat), satuan*, harga*, merek, toko, keterangan, aktif |
| PUT/DELETE | `/api/material/{material}` | DELETE = soft delete |

**Material**: `{ "id", "kode", "nama", "kategori", "kategori_label", "satuan", "harga" (float), "merek", "toko", "keterangan", "aktif" }`

## Analisa (AHSP)

| Metode | Path | Catatan |
|---|---|---|
| GET | `/api/analisa?kategori=&search=` | array `Analisa` tanpa `items` |
| GET | `/api/analisa/{analisa}` | dengan `items` |
| POST | `/api/analisa` | kode, nama*, kategori*, satuan*, keterangan, items: [{material_id*, koefisien*}] |
| PUT/DELETE | `/api/analisa/{analisa}` | PUT mengganti seluruh items bila `items` dikirim |

**Analisa**: `{ "id", "kode", "nama", "kategori", "kategori_label", "satuan", "keterangan", "harga_satuan" (float, dari harga katalog terkini), "items": [{ "id", "material_id", "nama", "kategori", "satuan", "koefisien", "harga", "subtotal" (float) }] }`

## Log harian

| Metode | Path | Catatan |
|---|---|---|
| GET | `/api/kegiatan/{kegiatan}/log-harian` | paginasi standar (`per_page`), urut tanggal desc |
| POST | `/api/kegiatan/{kegiatan}/log-harian` | tanggal*, cuaca, jumlah_pekerja, uraian*, kendala |
| PUT/DELETE | `/api/log-harian/{logHarian}` | pembuat sendiri atau pengelola |

**LogHarian**: `{ "id", "kegiatan_id", "tanggal" (Y-m-d), "cuaca", "cuaca_label", "jumlah_pekerja", "uraian", "kendala", "dibuat_oleh": {"id","name"}, "created_at" }`
