# ERD Event (tanpa atribut)

Sumber: `database/migrations` (foreign keys + relasi polymorphic via `morphs`).

```mermaid
erDiagram
    users ||--o{ event_registrations : registers
    events ||--o{ event_registrations : has

    events ||--o{ event_schedule_items : schedules
    events ||--o{ event_expenses : expenses

    events ||--o{ feedback : receives
    users ||--o{ feedback : writes

    users ||--o{ user_saved_events : saves
    events ||--o{ user_saved_events : saved

    events ||--o{ manual_payments : manual_payment
    event_registrations ||--o{ manual_payments : manual_payment
    users ||--o{ manual_payments : manual_payment

    manual_payments ||--o{ payment_proofs : has
    event_registrations ||--o{ payment_proofs : proof_for
    users ||--o{ payment_proofs : uploaded_by

    users ||--o{ payments : makes
    event_registrations ||--o{ payments : payable

    users ||--o{ certificates : owns
    event_registrations ||--o{ certificates : certifiable

    users ||--o{ user_notifications : notifies
    users ||--o{ activity_logs : logs
    users ||--o{ support_messages : sends
```

## Catatan

- `payments.payable` dan `certificates.certifiable` bersifat polymorphic; untuk konteks Event ini ditampilkan ke `event_registrations`.
- `user_saved_events` dibuat tanpa FK constraints di migrations; relasi user↔event di sini bersifat “logical”.
- Kolom `event_registrations.payment_verified_by` (FK ke `users`) ada di migration tambahan; relasinya secara konsep adalah “user memverifikasi pembayaran registrasi event”.

## Di luar scope Event (tidak ditampilkan)

- Struktur Course/LMS (categories/courses/enrollments/progress/quiz/reviews).
- Referral/withdrawal/broadcasts/learning_time_dailies/profile_reminders/login_otps, dll.

## Tabel yang terdeteksi tapi definisinya bermasalah

- Migration `2025_11_25_000001_create_event_manual_incomes_table.php` kosong, jadi relasi tabel itu tidak bisa dipastikan dari migrations.
