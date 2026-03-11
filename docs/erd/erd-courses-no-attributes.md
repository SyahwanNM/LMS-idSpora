# ERD Course (tanpa atribut)

Sumber: `database/migrations` (foreign keys + relasi polymorphic via `morphs`).

```mermaid
erDiagram
    categories ||--o{ courses : categorizes

    courses ||--o{ course_module : has

    courses ||--o{ enrollments : has
    users ||--o{ enrollments : enrolls

    enrollments ||--o{ progress : tracks
    course_module ||--o{ progress : tracks

    courses ||--o{ quizzez : has
    course_module ||--o{ quiz_questions : has
    quiz_questions ||--o{ quiz_answers : has

    users ||--o{ quiz_attempts : attempts
    course_module ||--o{ quiz_attempts : attempts

    users ||--o{ reviews : writes
    courses ||--o{ reviews : receives

    users ||--o{ payments : makes
    enrollments ||--o{ payments : payable

    users ||--o{ certificates : owns
    enrollments ||--o{ certificates : certifiable

    users ||--o{ learning_time_dailies : learns
    courses ||--o{ learning_time_dailies : course

    users ||--o{ manual_payments : manual_payment
    courses ||--o{ manual_payments : manual_payment
    enrollments ||--o{ manual_payments : manual_payment
```

## Catatan

- `payments.payable` dan `certificates.certifiable` bersifat polymorphic; untuk konteks Course ini ditampilkan ke `enrollments`.
- `course_module` adalah nama tabel modul (bukan jamak), sesuai migrations.
- `manual_payments` punya FK opsional ke `courses` dan `enrollments` (migration tambahan), jadi tetap relevan untuk flow pembayaran course manual.

## Di luar scope Course (tidak ditampilkan)

- Struktur Event (events/event_registrations/feedback/schedule/expenses/saved_events/payment_proofs).
- Notifikasi, logs, support, referral/withdrawal/broadcasts, dll.
