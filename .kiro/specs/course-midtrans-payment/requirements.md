# Dokumen Persyaratan

## Pendahuluan

Fitur ini menambahkan integrasi Midtrans Snap sebagai metode pembayaran untuk course berbayar pada aplikasi Laravel LMS. Saat ini course hanya mendukung pembayaran manual (QRIS/transfer + upload bukti bayar yang diverifikasi admin). Dengan fitur ini, user dapat memilih membayar via Midtrans Snap (kartu kredit, transfer bank, e-wallet, QRIS real-time) sehingga enrollment dapat diaktifkan secara otomatis tanpa intervensi admin.

Implementasi mengikuti pola yang sudah ada pada fitur Midtrans untuk event (`GET /api/events/{id}/midtrans/pending-order`, `GET /api/events/{id}/midtrans/snap-token`, `POST /api/events/{id}/midtrans/finalize`), dan menggunakan model `ManualPayment` (dengan `method = 'midtrans'`) serta `Enrollment` yang sudah ada.

## Glosarium

- **Course_Payment_API**: Kumpulan endpoint RESTful yang mengelola pembayaran Midtrans untuk course.
- **Course**: Entitas kursus berbayar dengan field `price`, `discount_percent`, `discount_start`, `discount_end`, `is_reseller_course`, `status`.
- **Enrollment**: Catatan pendaftaran user ke course, dengan status: `pending`, `active`, `completed`.
- **ManualPayment**: Model pembayaran yang digunakan untuk semua metode, termasuk Midtrans (`method = 'midtrans'`), dengan status: `pending`, `settled`, `rejected`, `expired`.
- **Snap_Token**: Token satu kali pakai yang diterbitkan Midtrans untuk membuka UI pembayaran Snap.
- **Order_ID**: Identifikasi unik transaksi dalam format `MT-CRS-{UNIQID}`, digunakan sebagai referensi ke Midtrans.
- **Webhook**: Notifikasi HTTP POST dari Midtrans ke endpoint publik aplikasi saat status transaksi berubah.
- **Authenticated_User**: Pengguna yang sudah login dan memiliki token Sanctum valid.
- **Admin**: Pengguna dengan role `admin`.
- **Midtrans_Service**: Layanan pihak ketiga Midtrans yang memproses pembayaran.

---

## Persyaratan

### Persyaratan 1: Mendapatkan Snap Token untuk Course

**User Story:** Sebagai Authenticated_User, saya ingin mendapatkan Snap Token untuk course berbayar, sehingga saya dapat membuka UI pembayaran Midtrans dan menyelesaikan transaksi.

#### Kriteria Penerimaan

1. WHEN Authenticated_User mengakses `GET /api/courses/{course}/midtrans/snap-token` pada course berbayar (`price > 0`) yang berstatus `active`, THE Course_Payment_API SHALL membuat atau menggunakan kembali Enrollment berstatus `pending` dan ManualPayment berstatus `pending` dengan `method = 'midtrans'`, lalu mengembalikan `snap_token`, `order_id`, `amount`, `client_key`, dan `is_production`.
2. WHEN Authenticated_User sudah memiliki ManualPayment Midtrans berstatus `pending` dengan `snap_token` valid di metadata dan tidak menyertakan parameter `force_new = true`, THE Course_Payment_API SHALL mengembalikan `snap_token` yang sudah ada tanpa membuat transaksi Midtrans baru.
3. WHEN Authenticated_User menyertakan parameter `force_new = true`, THE Course_Payment_API SHALL menandai ManualPayment lama sebagai `rejected` dan membuat Order ID serta Snap Token baru dari Midtrans.
4. IF Authenticated_User sudah memiliki Enrollment berstatus `active` untuk course tersebut, THEN THE Course_Payment_API SHALL mengembalikan HTTP 409 dengan pesan bahwa user sudah terdaftar.
5. IF Authenticated_User mengakses endpoint ini pada course gratis (`price = 0`), THEN THE Course_Payment_API SHALL mengembalikan HTTP 400 dengan pesan bahwa course gratis tidak memerlukan Midtrans.
6. IF Authenticated_User mengakses endpoint ini pada course yang tidak ada atau berstatus bukan `active`, THEN THE Course_Payment_API SHALL mengembalikan HTTP 404.
7. WHEN Midtrans_Service gagal menerbitkan Snap Token karena error jaringan atau konfigurasi, THE Course_Payment_API SHALL mengembalikan HTTP 502 dengan pesan error tanpa menyimpan ManualPayment baru.
8. THE Course_Payment_API SHALL membuat Order ID dengan format `MT-CRS-{UNIQID}` yang unik untuk setiap transaksi baru.

---

### Persyaratan 2: Cek Pending Order Midtrans untuk Course

**User Story:** Sebagai Authenticated_User, saya ingin mengecek apakah ada pending order Midtrans untuk course yang sedang saya beli, sehingga saya dapat melanjutkan pembayaran yang belum selesai.

#### Kriteria Penerimaan

1. WHEN Authenticated_User mengakses `GET /api/courses/{course}/midtrans/pending-order`, THE Course_Payment_API SHALL mengembalikan data ManualPayment Midtrans berstatus `pending` milik user untuk course tersebut, termasuk `order_id`, `amount`, `snap_token` (jika ada), dan `created_at`.
2. IF tidak ada pending order Midtrans untuk user dan course tersebut, THEN THE Course_Payment_API SHALL mengembalikan HTTP 404 dengan pesan bahwa tidak ada pending order.
3. IF Authenticated_User sudah memiliki Enrollment berstatus `active`, THEN THE Course_Payment_API SHALL mengembalikan HTTP 409 dengan pesan bahwa user sudah terdaftar.

---

### Persyaratan 3: Finalisasi Pembayaran Midtrans untuk Course

**User Story:** Sebagai Authenticated_User, saya ingin mengonfirmasi hasil pembayaran Midtrans setelah UI Snap ditutup, sehingga status enrollment saya diperbarui sesuai hasil transaksi.

#### Kriteria Penerimaan

1. WHEN Authenticated_User mengakses `POST /api/courses/{course}/midtrans/finalize` dengan `order_id` yang valid, THE Course_Payment_API SHALL mengambil status transaksi dari Midtrans_Service dan memperbarui status ManualPayment sesuai hasil.
2. WHEN status transaksi dari Midtrans_Service adalah `settlement` atau `capture` (tanpa fraud challenge), THE Course_Payment_API SHALL memperbarui ManualPayment menjadi `settled` dan Enrollment menjadi `active` secara atomik dalam satu transaksi database.
3. WHEN status transaksi dari Midtrans_Service adalah `pending`, THE Course_Payment_API SHALL mempertahankan status ManualPayment sebagai `pending` dan mengembalikan informasi bahwa pembayaran sedang diproses.
4. WHEN status transaksi dari Midtrans_Service adalah `expire`, THE Course_Payment_API SHALL memperbarui ManualPayment menjadi `expired` dan Enrollment tetap `pending`.
5. WHEN status transaksi dari Midtrans_Service adalah `deny` atau `cancel`, THE Course_Payment_API SHALL memperbarui ManualPayment menjadi `rejected` dan Enrollment tetap `pending`.
6. IF `order_id` pada request tidak ditemukan dalam ManualPayment milik user untuk course tersebut, THEN THE Course_Payment_API SHALL mengembalikan HTTP 404.
7. IF ManualPayment sudah berstatus `settled`, THEN THE Course_Payment_API SHALL mengembalikan HTTP 200 dengan pesan bahwa pembayaran sudah berhasil tanpa memanggil Midtrans_Service ulang (idempoten).
8. WHEN Midtrans_Service tidak dapat dihubungi saat finalisasi, THE Course_Payment_API SHALL mengembalikan HTTP 502 dan tidak mengubah status ManualPayment.

---

### Persyaratan 4: Webhook Midtrans untuk Course

**User Story:** Sebagai sistem, saya ingin menerima notifikasi otomatis dari Midtrans saat status pembayaran berubah, sehingga Enrollment dapat diaktifkan tanpa user harus memanggil endpoint finalize secara manual.

#### Kriteria Penerimaan

1. WHEN Midtrans_Service mengirim POST ke endpoint webhook dengan `order_id` yang diawali `MT-CRS-`, THE Course_Payment_API SHALL memproses notifikasi dan memperbarui status ManualPayment serta Enrollment yang sesuai.
2. WHEN notifikasi webhook diterima dengan `transaction_status = settlement` atau `capture` (tanpa fraud challenge), THE Course_Payment_API SHALL memperbarui ManualPayment menjadi `settled` dan Enrollment menjadi `active` secara atomik.
3. WHEN notifikasi webhook diterima dengan `transaction_status = expire`, THE Course_Payment_API SHALL memperbarui ManualPayment menjadi `expired`.
4. WHEN notifikasi webhook diterima dengan `transaction_status = deny` atau `cancel`, THE Course_Payment_API SHALL memperbarui ManualPayment menjadi `rejected`.
5. THE Course_Payment_API SHALL memverifikasi signature key pada setiap notifikasi webhook menggunakan `SHA512(order_id + status_code + gross_amount + server_key)` sebelum memproses.
6. IF signature key pada notifikasi webhook tidak valid, THEN THE Course_Payment_API SHALL mengembalikan HTTP 403 dan tidak memproses notifikasi.
7. THE Course_Payment_API SHALL mengembalikan HTTP 200 untuk semua notifikasi webhook yang berhasil diproses, termasuk notifikasi duplikat untuk order yang sudah `settled` (idempoten).
8. IF `order_id` pada notifikasi webhook tidak ditemukan dalam database, THEN THE Course_Payment_API SHALL mengembalikan HTTP 404 dan mencatat log warning.

---

### Persyaratan 5: Sinkronisasi Status Midtrans via Scheduled Command

**User Story:** Sebagai sistem, saya ingin status pembayaran Midtrans untuk course disinkronkan secara berkala, sehingga pembayaran yang berhasil di lingkungan tanpa webhook tetap dapat diproses.

#### Kriteria Penerimaan

1. WHEN command `midtrans:sync-status` dijalankan, THE Course_Payment_API SHALL memeriksa status ManualPayment dengan `method = 'midtrans'`, `status = 'pending'`, dan `course_id` tidak null yang sudah lebih dari 15 menit sejak dibuat.
2. WHEN status dari Midtrans_Service adalah `settled`, THE Course_Payment_API SHALL memperbarui ManualPayment menjadi `settled` dan Enrollment terkait menjadi `active`.
3. WHEN status dari Midtrans_Service adalah `expired` atau `rejected`, THE Course_Payment_API SHALL memperbarui ManualPayment dan Enrollment sesuai status tersebut.
4. IF Midtrans_Service mengembalikan HTTP 404 untuk suatu `order_id`, THEN THE Course_Payment_API SHALL menandai ManualPayment tersebut sebagai `expired`.
5. THE Course_Payment_API SHALL mencatat log untuk setiap perubahan status yang terjadi selama sinkronisasi.

---

### Persyaratan 6: Diskon Referral pada Pembayaran Midtrans Course

**User Story:** Sebagai Authenticated_User, saya ingin menggunakan kode referral saat membayar course via Midtrans, sehingga saya mendapatkan diskon yang sama seperti pada pembayaran manual.

#### Kriteria Penerimaan

1. WHEN Authenticated_User menyertakan `referral_code` yang valid pada request `GET /api/courses/{course}/midtrans/snap-token` untuk course dengan `is_reseller_course = true`, THE Course_Payment_API SHALL menerapkan diskon referral sebesar 10% dari harga akhir dan menggunakan jumlah yang sudah didiskon sebagai `gross_amount` pada transaksi Midtrans.
2. IF `referral_code` yang diberikan adalah kode milik user itu sendiri, THEN THE Course_Payment_API SHALL mengabaikan kode referral dan menggunakan harga penuh tanpa diskon.
3. IF course tidak memiliki `is_reseller_course = true`, THEN THE Course_Payment_API SHALL mengabaikan `referral_code` yang diberikan dan menggunakan harga penuh.
4. WHEN diskon referral diterapkan, THE Course_Payment_API SHALL menyimpan `referral_code` dan `discount_rate` pada field `metadata` ManualPayment.

---

### Persyaratan 7: Konsistensi dengan Pembayaran Manual yang Sudah Ada

**User Story:** Sebagai Authenticated_User, saya ingin metode pembayaran Midtrans dan manual dapat digunakan secara bergantian tanpa konflik, sehingga saya tidak terjebak dalam status pembayaran yang tidak konsisten.

#### Kriteria Penerimaan

1. WHEN Authenticated_User memiliki ManualPayment berstatus `pending` dengan `method = 'qris'` dan mengakses endpoint Midtrans snap-token, THE Course_Payment_API SHALL menandai ManualPayment lama sebagai `rejected` sebelum membuat ManualPayment baru dengan `method = 'midtrans'`.
2. WHEN Authenticated_User memiliki ManualPayment berstatus `pending` dengan `method = 'midtrans'` dan mengakses endpoint upload bukti bayar (`POST /api/courses/{course}/payment-proof`), THE Course_Payment_API SHALL menandai ManualPayment Midtrans lama sebagai `rejected` sebelum membuat ManualPayment baru dengan `method = 'qris'`.
3. THE Course_Payment_API SHALL memastikan hanya ada satu ManualPayment berstatus `pending` per kombinasi `user_id` dan `course_id` pada satu waktu.
4. WHEN Enrollment berstatus `active` (dari pembayaran manual yang sudah disetujui admin), THE Course_Payment_API SHALL mengembalikan HTTP 409 jika user mencoba membuat transaksi Midtrans baru untuk course yang sama.

---

### Persyaratan 8: Format Response dan Penanganan Error

**User Story:** Sebagai developer yang mengintegrasikan API, saya ingin format response yang konsisten pada semua endpoint Midtrans course, sehingga integrasi dapat dilakukan dengan mudah.

#### Kriteria Penerimaan

1. THE Course_Payment_API SHALL mengembalikan seluruh response dalam format JSON dengan struktur: `{ "status": "success"|"error", "message": string, "data": object|null, "pagination": null }`.
2. IF terjadi error validasi pada request, THEN THE Course_Payment_API SHALL mengembalikan HTTP 422 dengan detail field yang tidak valid.
3. IF request dilakukan tanpa token autentikasi Sanctum yang valid, THEN THE Course_Payment_API SHALL mengembalikan HTTP 401.
4. THE Course_Payment_API SHALL menerapkan rate limiting sebesar 100 request/menit untuk semua endpoint Midtrans course yang memerlukan autentikasi.
5. IF terjadi error server internal yang tidak terduga, THEN THE Course_Payment_API SHALL mengembalikan HTTP 500 dengan pesan error tanpa mengekspos stack trace ke client.
