# USER MANUAL — RESTful API Admin Event

---

## INFORMASI UMUM

| Item | Keterangan |
|---|---|
| Base URL | `https://{domain}/api` |
| Format Data | JSON |
| Autentikasi | Bearer Token (Laravel Sanctum) |
| Role yang Diizinkan | Admin |
| Rate Limit | 60 request/menit |

### Header Wajib

Semua endpoint admin wajib menyertakan header berikut:

```
Authorization: Bearer {token}
Accept: application/json
```

Untuk request yang mengirim data form (store/update), gunakan:

```
Content-Type: multipart/form-data
```

### Format Response Standar

**Sukses:**
```json
{
  "status": "success",
  "message": "Pesan sukses",
  "data": { ... }
}
```

**Error:**
```json
{
  "status": "error",
  "message": "Pesan error",
  "data": null
}
```

---

## DAFTAR ENDPOINT

| No | Method | Endpoint | Deskripsi |
|---|---|---|---|
| 1 | GET | `/api/admin/events` | Daftar semua event |
| 2 | GET | `/api/admin/events/{id}` | Detail satu event |
| 3 | POST | `/api/admin/events` | Buat event baru |
| 4 | PUT/PATCH | `/api/admin/events/{id}` | Update event |
| 5 | DELETE | `/api/admin/events/{id}` | Hapus event |
| 6 | GET | `/api/admin/reports/events/growth` | Laporan pertumbuhan event |

---

## 1. GET /api/admin/events — Daftar Event

Mengambil daftar semua event dengan dukungan filter, pencarian, dan pengurutan.

### Query Parameters (Opsional)

| Parameter | Tipe | Keterangan |
|---|---|---|
| `per_page` | integer (1–100) | Jumlah data per halaman. Default: 10 |
| `search` | string | Cari berdasarkan judul atau nama speaker |
| `status` | string | Filter status: `upcoming`, `ongoing`, `finished` |
| `manage_action` | string | Tipe pengelolaan: `manage` atau `create` |
| `event_month` | string | Filter bulan event, format: `YYYY-MM` (contoh: `2026-04`) |
| `jenis` | string | Filter berdasarkan jenis/kategori event |
| `is_published` | boolean | Filter status publikasi: `true` atau `false` |
| `date_from` | date | Filter tanggal mulai (format: `YYYY-MM-DD`) |
| `date_to` | date | Filter tanggal akhir (format: `YYYY-MM-DD`) |
| `price_min` | numeric | Filter harga minimum |
| `price_max` | numeric | Filter harga maksimum |
| `trainer_id` | integer | Filter berdasarkan ID trainer |
| `sort_by` | string | Kolom pengurutan: `event_date`, `price`, `title`, `created_at`. Default: `created_at` |
| `sort_dir` | string | Arah pengurutan: `asc` atau `desc`. Default: `desc` |

### Contoh Request

```
GET /api/admin/events?status=upcoming&per_page=5&sort_by=event_date&sort_dir=asc
Authorization: Bearer {token}
```

### Contoh Response (200 OK)

```json
{
  "status": "success",
  "message": "Daftar event (admin)",
  "filters": {
    "search": null,
    "status": "upcoming",
    "manage_action": null,
    "event_month": null,
    "jenis": null,
    "is_published": null,
    "date_from": null,
    "date_to": null,
    "price_min": null,
    "price_max": null,
    "trainer_id": null,
    "sort_by": "event_date",
    "sort_dir": "asc"
  },
  "data": [
    {
      "id": 12,
      "title": "Workshop Laravel Advanced",
      "speaker": "Budi Santoso",
      "event_date": "2026-06-15",
      "event_time": "09:00",
      "event_time_end": "17:00",
      "location": "Jakarta",
      "price": "500000.00",
      "is_published": true,
      "manage_action": "create",
      "jenis": "Workshop"
    }
  ],
  "pagination": {
    "current_page": 1,
    "per_page": 5,
    "total": 1,
    "last_page": 1
  }
}
```

---

## 2. GET /api/admin/events/{id} — Detail Event

Mengambil detail lengkap satu event beserta jadwal (schedule) dan pengeluaran (expenses).

### Path Parameter

| Parameter | Tipe | Keterangan |
|---|---|---|
| `id` | integer | ID event |

### Contoh Request

```
GET /api/admin/events/12
Authorization: Bearer {token}
```

### Contoh Response (200 OK)

```json
{
  "status": "success",
  "message": "Detail event",
  "data": {
    "id": 12,
    "title": "Workshop Laravel Advanced",
    "speaker": "Budi Santoso",
    "materi": "Laravel, API, Testing",
    "jenis": "Workshop",
    "short_description": "Workshop intensif Laravel untuk developer menengah.",
    "description": "<p>Deskripsi lengkap event...</p>",
    "benefit": "Sertifikat, Materi PDF, Networking",
    "terms_and_conditions": "Peserta wajib membawa laptop.",
    "location": "Gedung Serbaguna, Jakarta",
    "maps_url": "https://maps.google.com/?q=...",
    "latitude": "-6.2000000",
    "longitude": "106.8166700",
    "zoom_link": null,
    "price": "500000.00",
    "discount_percentage": 10,
    "discount_until": "2026-06-01",
    "event_date": "2026-06-15",
    "event_time": "09:00",
    "event_time_end": "17:00",
    "material_deadline": "2026-06-10T00:00:00.000000Z",
    "is_published": true,
    "published_at": "2026-05-01T08:00:00.000000Z",
    "manage_action": "create",
    "image": "events/workshop-laravel.jpg",
    "schedule_items": [
      {
        "id": 1,
        "start": "09:00",
        "end": "10:00",
        "title": "Pembukaan",
        "description": "Sambutan dan perkenalan"
      }
    ],
    "expenses": [
      {
        "id": 1,
        "item": "Sewa Ruangan",
        "quantity": 1,
        "unit_price": 2000000,
        "total": 2000000
      }
    ],
    "created_at": "2026-05-01T07:00:00.000000Z",
    "updated_at": "2026-05-01T08:00:00.000000Z"
  }
}
```

### Response Error

| Kode | Keterangan |
|---|---|
| 404 | Event tidak ditemukan |

---

## 3. POST /api/admin/events — Buat Event Baru

Membuat event baru. Request menggunakan `multipart/form-data` karena mendukung upload gambar.

### Body Parameters

#### Field Wajib

| Field | Tipe | Keterangan |
|---|---|---|
| `title` | string (maks. 255) | Judul event |
| `speaker` | string (maks. 255) | Nama speaker/pembicara |
| `manage_action` | string | Tipe pengelolaan: `manage` atau `create` |
| `short_description` | string | Deskripsi singkat event |
| `description` | string | Deskripsi lengkap event (boleh HTML) |
| `location` | string (maks. 255) | Lokasi event |
| `price` | numeric (≥ 0) | Harga event (isi `0` untuk gratis) |
| `event_date` | date | Tanggal event (format: `YYYY-MM-DD`) |
| `event_time` | time | Jam mulai event (format: `HH:MM`) |

#### Field Opsional

| Field | Tipe | Keterangan |
|---|---|---|
| `materi` | string (maks. 255) | Topik/materi event |
| `jenis` | string (maks. 100) | Jenis/kategori event |
| `benefit` | string | Manfaat yang didapat peserta |
| `terms_and_conditions` | string | Syarat dan ketentuan |
| `maps_url` | string (maks. 512) | URL Google Maps |
| `latitude` | numeric (-90 s/d 90) | Koordinat latitude |
| `longitude` | numeric (-180 s/d 180) | Koordinat longitude |
| `zoom_link` | URL (maks. 255) | Link Zoom untuk event online |
| `discount_percentage` | integer (0–100) | Persentase diskon |
| `discount_until` | date | Batas berlaku diskon |
| `event_time_end` | time | Jam selesai event |
| `material_deadline` | date | Batas upload materi (harus sebelum `event_date`) |
| `image` | file (jpg/jpeg/png, maks. 5 MB) | Gambar/poster event |
| `is_published` | boolean | Status publikasi (`true`/`false`). Default: `false` |
| `published_at` | datetime | Waktu publikasi (otomatis diisi `now()` jika `is_published=true` dan tidak diisi) |
| `schedule` | array | Daftar jadwal acara (lihat format di bawah) |
| `expenses` | array | Daftar pengeluaran event (lihat format di bawah) |

#### Format Array `schedule`

| Field | Tipe | Keterangan |
|---|---|---|
| `schedule[0][start]` | string | Jam mulai sesi (contoh: `09:00`) |
| `schedule[0][end]` | string | Jam selesai sesi (contoh: `10:00`) |
| `schedule[0][title]` | string (maks. 255) | Judul sesi |
| `schedule[0][description]` | string (maks. 500) | Deskripsi sesi |

#### Format Array `expenses`

| Field | Tipe | Keterangan |
|---|---|---|
| `expenses[0][item]` | string (maks. 255) | Nama item pengeluaran |
| `expenses[0][quantity]` | numeric (≥ 0) | Jumlah |
| `expenses[0][unit_price]` | numeric (≥ 0) | Harga satuan |

### Contoh Request (multipart/form-data)

```
POST /api/admin/events
Authorization: Bearer {token}
Content-Type: multipart/form-data

title              = Workshop Laravel Advanced
speaker            = Budi Santoso
manage_action      = create
short_description  = Workshop intensif Laravel untuk developer menengah.
description        = <p>Deskripsi lengkap event...</p>
location           = Gedung Serbaguna, Jakarta
price              = 500000
event_date         = 2026-06-15
event_time         = 09:00
event_time_end     = 17:00
discount_percentage= 10
discount_until     = 2026-06-01
is_published       = true
image              = [file: poster.jpg]
schedule[0][start] = 09:00
schedule[0][end]   = 10:00
schedule[0][title] = Pembukaan
expenses[0][item]  = Sewa Ruangan
expenses[0][quantity] = 1
expenses[0][unit_price] = 2000000
```

### Contoh Response (201 Created)

```json
{
  "status": "success",
  "message": "Event berhasil dibuat",
  "data": {
    "id": 13,
    "title": "Workshop Laravel Advanced",
    "speaker": "Budi Santoso",
    "location": "Gedung Serbaguna, Jakarta",
    "price": "500000.00",
    "event_date": "2026-06-15",
    "event_time": "09:00",
    "event_time_end": "17:00",
    "is_published": true,
    "published_at": "2026-05-01T08:00:00.000000Z",
    "schedule_items": [ ... ],
    "expenses": [ ... ],
    "created_at": "2026-05-01T08:00:00.000000Z",
    "updated_at": "2026-05-01T08:00:00.000000Z"
  }
}
```

### Response Error

| Kode | Keterangan |
|---|---|
| 422 | Validasi gagal — cek field yang wajib diisi |

---

## 4. PUT/PATCH /api/admin/events/{id} — Update Event

Memperbarui data event yang sudah ada. Menggunakan `multipart/form-data`. Semua field schedule dan expenses akan **diganti seluruhnya** (bukan ditambahkan).

### Path Parameter

| Parameter | Tipe | Keterangan |
|---|---|---|
| `id` | integer | ID event yang akan diupdate |

### Body Parameters

Sama persis dengan endpoint **Buat Event Baru** (POST). Field `image` bersifat opsional — jika tidak dikirim, gambar lama tetap dipertahankan.

**Catatan khusus `is_published`:**
- Jika `is_published = true` dan `published_at` tidak diisi, maka `published_at` akan diisi dengan waktu saat ini atau waktu publikasi sebelumnya.
- Jika `is_published = false`, maka `published_at` akan dikosongkan (null).

### Contoh Request

```
PUT /api/admin/events/13
Authorization: Bearer {token}
Content-Type: multipart/form-data

title              = Workshop Laravel Advanced (Updated)
speaker            = Budi Santoso
manage_action      = create
short_description  = Workshop intensif Laravel — edisi terbaru.
description        = <p>Deskripsi diperbarui...</p>
location           = Gedung Serbaguna, Jakarta
price              = 450000
event_date         = 2026-06-15
event_time         = 09:00
is_published       = true
```

### Contoh Response (200 OK)

```json
{
  "status": "success",
  "message": "Event berhasil diupdate",
  "data": {
    "id": 13,
    "title": "Workshop Laravel Advanced (Updated)",
    "price": "450000.00",
    "updated_at": "2026-05-01T09:00:00.000000Z"
  }
}
```

### Response Error

| Kode | Keterangan |
|---|---|
| 404 | Event tidak ditemukan |
| 422 | Validasi gagal |

---

## 5. DELETE /api/admin/events/{id} — Hapus Event

Menghapus event (soft delete — data tidak benar-benar dihapus dari database).

### Path Parameter

| Parameter | Tipe | Keterangan |
|---|---|---|
| `id` | integer | ID event yang akan dihapus |

### Contoh Request

```
DELETE /api/admin/events/13
Authorization: Bearer {token}
```

### Contoh Response (200 OK)

```json
{
  "status": "success",
  "message": "Event berhasil dihapus"
}
```

### Response Error

| Kode | Keterangan |
|---|---|
| 404 | Event tidak ditemukan |

---

## 6. GET /api/admin/reports/events/growth — Laporan Pertumbuhan Event

Mengambil laporan pertumbuhan event per bulan, termasuk ringkasan statistik, data grafik harian, dan daftar event beserta jumlah peserta dan rating.

### Query Parameters (Opsional)

| Parameter | Tipe | Keterangan |
|---|---|---|
| `month` | string | Bulan laporan, format: `YYYY-MM` (contoh: `2026-05`). Default: bulan berjalan |
| `search` | string | Cari berdasarkan judul event atau nama speaker |
| `manage_action` | string | Filter tipe pengelolaan: `manage` atau `create` |
| `price_type` | string | Filter tipe harga: `free` (gratis) atau `paid` (berbayar) |

### Contoh Request

```
GET /api/admin/reports/events/growth?month=2026-05&price_type=paid
Authorization: Bearer {token}
```

### Contoh Response (200 OK)

```json
{
  "status": "success",
  "message": "Growth report event",
  "meta": {
    "month": "2026-05",
    "filters": {
      "search": null,
      "manage_action": null,
      "price_type": "paid"
    }
  },
  "summary": {
    "total_events": 8,
    "total_participants": 320,
    "total_free_participants": 0,
    "total_paid_participants": 320,
    "total_manage_events": 3,
    "total_create_events": 5
  },
  "chart": {
    "labels": [1, 2, 3, 4, 5, "...", 31],
    "series": {
      "free_participants": [0, 0, 0, "..."],
      "paid_participants": [50, 0, 80, "..."]
    },
    "composition": {
      "free": 0,
      "paid": 8,
      "manage": 3,
      "create": 5
    }
  },
  "rows": [
    {
      "id": 12,
      "name": "Workshop Laravel Advanced",
      "date": "2026-05-15",
      "participants": 80,
      "speaker": "Budi Santoso",
      "manage_action": "create",
      "price": 500000,
      "is_free": false,
      "event_rating": 4.5,
      "speaker_rating": 4.8
    }
  ]
}
```

### Keterangan Field Response

| Field | Keterangan |
|---|---|
| `summary.total_events` | Total event pada bulan yang dipilih |
| `summary.total_participants` | Total peserta dari semua event |
| `summary.total_free_participants` | Total peserta event gratis |
| `summary.total_paid_participants` | Total peserta event berbayar |
| `summary.total_manage_events` | Jumlah event dengan tipe `manage` |
| `summary.total_create_events` | Jumlah event dengan tipe `create` |
| `chart.labels` | Label hari (1 s/d jumlah hari dalam bulan) |
| `chart.series.free_participants` | Jumlah peserta gratis per hari |
| `chart.series.paid_participants` | Jumlah peserta berbayar per hari |
| `chart.composition` | Komposisi event (free/paid/manage/create) |
| `rows[].event_rating` | Rata-rata rating event (null jika belum ada feedback) |
| `rows[].speaker_rating` | Rata-rata rating speaker (null jika belum ada feedback) |

---

## KODE HTTP YANG DIGUNAKAN

| Kode | Keterangan |
|---|---|
| 200 | OK — Request berhasil |
| 201 | Created — Data berhasil dibuat |
| 401 | Unauthorized — Token tidak valid atau tidak disertakan |
| 403 | Forbidden — Akses ditolak (bukan admin) |
| 404 | Not Found — Data tidak ditemukan |
| 422 | Unprocessable Entity — Validasi gagal |
| 429 | Too Many Requests — Melebihi batas rate limit (60 req/menit) |
| 500 | Internal Server Error — Kesalahan pada server |

---

## CATATAN PENTING

1. **Autentikasi**: Semua endpoint admin memerlukan token Sanctum yang valid dengan role `admin`. Token diperoleh melalui endpoint login (`POST /api/login`).

2. **Upload Gambar**: Field `image` hanya menerima format `jpg`, `jpeg`, atau `png` dengan ukuran maksimal **5 MB**.

3. **Schedule & Expenses**: Saat melakukan update event, seluruh data schedule dan expenses lama akan **dihapus dan diganti** dengan data baru yang dikirim. Jika tidak mengirim array `schedule` atau `expenses`, data lama akan dikosongkan.

4. **Soft Delete**: Endpoint DELETE tidak menghapus data secara permanen. Data event masih tersimpan di database dengan kolom `deleted_at` terisi.

5. **Publikasi Event**: Event yang belum dipublikasikan (`is_published = false`) tidak akan muncul di endpoint publik (`GET /api/events`), tetapi tetap terlihat di endpoint admin.

6. **Material Deadline**: Field `material_deadline` harus bernilai hari ini atau setelahnya, dan harus sebelum `event_date`.
