# Dokumentasi Sumber Data Feedback Analysis

Dokumen ini menjelaskan dari tabel dan kolom mana setiap section di halaman Feedback Analysis mengambil datanya.

---

## 📊 **1. STATISTICS CARDS (Kartu Statistik)**

### **1.1 Total Feedback**
- **Tabel:** `feedback`
- **Kolom:** `id` (dihitung dengan `COUNT(*)`)
- **Query:** 
  ```php
  Feedback::query()->count()
  ```
- **Filter:** Dapat difilter berdasarkan `created_at` (date_from dan date_to)
- **Lokasi Controller:** Line 227 di `CRMController.php`

---

### **1.2 Rating Rata-rata**
- **Tabel:** `feedback`
- **Kolom:** `rating` (dihitung dengan `AVG(rating)`)
- **Query:**
  ```php
  Feedback::query()->avg('rating')
  ```
- **Filter:** Dapat difilter berdasarkan `created_at` (date_from dan date_to)
- **Lokasi Controller:** Line 228 di `CRMController.php`
- **Tipe Data:** `unsignedTinyInteger` (1-5)

---

### **1.3 Rating Speaker**
- **Tabel:** `feedback`
- **Kolom:** `speaker_rating` (dihitung dengan `AVG(speaker_rating)`)
- **Query:**
  ```php
  Feedback::whereNotNull('speaker_rating')->avg('speaker_rating')
  ```
- **Filter:** 
  - Hanya menghitung feedback yang memiliki `speaker_rating` (tidak NULL)
  - Dapat difilter berdasarkan `created_at` (date_from dan date_to)
- **Lokasi Controller:** Line 231-233 di `CRMController.php`
- **Tipe Data:** `unsignedTinyInteger` (1-5, nullable)
- **Catatan:** Menampilkan "-" jika tidak ada data

---

### **1.4 Rating Panitia**
- **Tabel:** `feedback`
- **Kolom:** `committee_rating` (dihitung dengan `AVG(committee_rating)`)
- **Query:**
  ```php
  Feedback::whereNotNull('committee_rating')->avg('committee_rating')
  ```
- **Filter:** 
  - Hanya menghitung feedback yang memiliki `committee_rating` (tidak NULL)
  - Dapat difilter berdasarkan `created_at` (date_from dan date_to)
- **Lokasi Controller:** Line 232-234 di `CRMController.php`
- **Tipe Data:** `unsignedTinyInteger` (1-5, nullable)
- **Catatan:** Menampilkan "-" jika tidak ada data

---

## 📈 **2. DISTRIBUSI RATING (Rating Distribution Chart)**

- **Tabel:** `feedback`
- **Kolom:** 
  - `rating` (untuk grouping)
  - `id` (untuk COUNT)
- **Query:**
  ```php
  Feedback::select('rating', DB::raw('count(*) as count'))
      ->groupBy('rating')
      ->orderBy('rating', 'desc')
      ->get()
  ```
- **Filter:** Dapat difilter berdasarkan `created_at` (date_from dan date_to)
- **Lokasi Controller:** Line 237-241 di `CRMController.php`
- **Output:** 
  - Menampilkan distribusi rating 1-5
  - Setiap bar menampilkan jumlah dan persentase feedback untuk rating tersebut
- **Perhitungan Persentase:**
  ```php
  $percentage = ($count / $totalFeedback) * 100
  ```

---

## 🏆 **3. EVENT TERBAIK (Top Rated Events)**

- **Tabel Utama:** `events`
- **Tabel Relasi:** `feedback` (melalui relasi `feedbacks()`)
- **Kolom dari `events`:**
  - `id`
  - `title`
  - `event_date`
- **Kolom dari `feedback`:**
  - `rating` (untuk `AVG(rating)`)
  - `speaker_rating` (untuk `AVG(speaker_rating)`)
  - `committee_rating` (untuk `AVG(committee_rating)`)
  - `id` (untuk `COUNT(*)`)
- **Query:**
  ```php
  Event::withCount('feedbacks')
      ->whereHas('feedbacks', function($q) use ($dateFrom, $dateTo) {
          // Filter by date if provided
      })
      ->withAvg('feedbacks', 'rating')
      ->withAvg('feedbacks', 'speaker_rating')
      ->withAvg('feedbacks', 'committee_rating')
      ->orderBy('feedbacks_avg_rating', 'desc')
      ->limit(10)
      ->get()
  ```
- **Filter:** 
  - Hanya event yang memiliki feedback
  - Dapat difilter berdasarkan `feedback.created_at` (date_from dan date_to)
- **Lokasi Controller:** Line 244-258 di `CRMController.php`
- **Output:**
  - Top 10 event dengan rating tertinggi
  - Menampilkan: `title`, `event_date`, `feedbacks_avg_rating`, `feedbacks_count`

---

## 📋 **4. DAFTAR EVENT DENGAN FEEDBACK (Events List with Feedback)**

- **Tabel Utama:** `events`
- **Tabel Relasi:** `feedback` (melalui relasi `feedbacks()`)
- **Kolom dari `events`:**
  - `id`
  - `title`
  - `event_date`
- **Kolom dari `feedback`:**
  - `rating` (untuk `AVG(rating)` per event)
  - `speaker_rating` (untuk `AVG(speaker_rating)` per event)
  - `committee_rating` (untuk `AVG(committee_rating)` per event)
  - `id` (untuk `COUNT(*)` per event)
- **Query:**
  ```php
  Event::withCount(['registrations', 'feedbacks'])
      ->whereHas('feedbacks', function($q) use ($dateFrom, $dateTo) {
          // Filter by date if provided
      })
      ->orderBy('event_date', 'desc')
      ->paginate(15)
  ```
- **Filter:** 
  - Dapat difilter berdasarkan `event_id` (spesifik event)
  - Dapat difilter berdasarkan `feedback.created_at` (date_from dan date_to)
- **Lokasi Controller:** Line 209-224 di `CRMController.php`
- **Perhitungan di View (Line 231-233):**
  ```php
  // Rating rata-rata per event
  Feedback::where('event_id', $event->id)->avg('rating')
  
  // Rating speaker rata-rata per event
  Feedback::where('event_id', $event->id)
      ->whereNotNull('speaker_rating')
      ->avg('speaker_rating')
  
  // Rating panitia rata-rata per event
  Feedback::where('event_id', $event->id)
      ->whereNotNull('committee_rating')
      ->avg('committee_rating')
  ```
- **Output:**
  - Tabel dengan kolom: Event, Tanggal Event, Jumlah Feedback, Rating Rata-rata, Rating Speaker, Rating Panitia, Aksi

---

## 🔍 **5. ANALISIS DETAIL EVENT (Event Detail Analysis)**

**Muncul hanya jika event_id dipilih di filter**

### **5.1 Statistik Event**
- **Tabel Utama:** `events`
- **Tabel Relasi:** `feedback`
- **Kolom:**
  - `feedbacks_avg_rating` (dari `AVG(feedback.rating)`)
  - `feedbacks_avg_speaker_rating` (dari `AVG(feedback.speaker_rating)`)
  - `feedbacks_avg_committee_rating` (dari `AVG(feedback.committee_rating)`)
- **Query:**
  ```php
  Event::withCount('feedbacks')
      ->withAvg('feedbacks', 'rating')
      ->withAvg('feedbacks', 'speaker_rating')
      ->withAvg('feedbacks', 'committee_rating')
      ->find($eventId)
  ```
- **Lokasi Controller:** Line 273-277 di `CRMController.php`

---

### **5.2 Distribusi Rating Event**
- **Tabel:** `feedback`
- **Kolom:**
  - `rating` (untuk grouping)
  - `id` (untuk COUNT)
- **Query:**
  ```php
  Feedback::where('event_id', $eventId)
      ->select('rating', DB::raw('count(*) as count'))
      ->groupBy('rating')
      ->orderBy('rating', 'desc')
      ->get()
  ```
- **Lokasi Controller:** Line 285-289 di `CRMController.php`
- **Perhitungan Persentase:**
  ```php
  $percentage = ($count / $totalFeedbacks) * 100
  ```

---

### **5.3 Daftar Feedback Event**
- **Tabel Utama:** `feedback`
- **Tabel Relasi:** `users` (melalui relasi `user()`)
- **Kolom dari `feedback`:**
  - `id`
  - `event_id`
  - `user_id`
  - `rating`
  - `speaker_rating`
  - `committee_rating`
  - `comment`
  - `created_at`
- **Kolom dari `users`:**
  - `id`
  - `name`
  - `avatar` (untuk avatar_url)
- **Query:**
  ```php
  Feedback::where('event_id', $eventId)
      ->with('user')
      ->orderBy('created_at', 'desc')
      ->get()
  ```
- **Lokasi Controller:** Line 280-283 di `CRMController.php`
- **Output:**
  - Menampilkan semua feedback untuk event tersebut
  - Setiap feedback menampilkan: user name, avatar, rating, speaker_rating, committee_rating, comment, created_at

---

## 📝 **6. FEEDBACK TERBARU (Recent Feedbacks)**

- **Tabel Utama:** `feedback`
- **Tabel Relasi:** 
  - `users` (melalui relasi `user()`)
  - `events` (melalui relasi `event()`)
- **Kolom dari `feedback`:**
  - `id`
  - `event_id`
  - `user_id`
  - `rating`
  - `comment`
  - `created_at`
- **Kolom dari `users`:**
  - `id`
  - `name`
  - `avatar` (untuk avatar_url)
- **Kolom dari `events`:**
  - `id`
  - `title`
- **Query:**
  ```php
  Feedback::with(['user', 'event'])
      ->whereDate('created_at', '>=', $dateFrom) // if provided
      ->whereDate('created_at', '<=', $dateTo)   // if provided
      ->orderBy('created_at', 'desc')
      ->limit(10)
      ->get()
  ```
- **Filter:** Dapat difilter berdasarkan `created_at` (date_from dan date_to)
- **Lokasi Controller:** Line 261-268 di `CRMController.php`
- **Output:**
  - 10 feedback terbaru
  - Menampilkan: user name, avatar, event title, rating, comment (dibatasi 100 karakter), created_at

---

## 🔧 **7. FILTER DROPDOWN (All Events for Filter)**

- **Tabel:** `events`
- **Kolom:**
  - `id`
  - `title`
- **Query:**
  ```php
  Event::whereHas('feedbacks')
      ->orderBy('title')
      ->get()
  ```
- **Lokasi Controller:** Line 300-302 di `CRMController.php`
- **Output:**
  - Dropdown berisi semua event yang memiliki feedback
  - Digunakan untuk filter event di form

---

## 📊 **RINGKASAN TABEL YANG DIGUNAKAN**

### **Tabel `feedback`**
Kolom yang digunakan:
- `id` (Primary Key)
- `event_id` (Foreign Key ke `events.id`)
- `user_id` (Foreign Key ke `users.id`)
- `rating` (unsignedTinyInteger, 1-5)
- `speaker_rating` (unsignedTinyInteger, 1-5, nullable)
- `committee_rating` (unsignedTinyInteger, 1-5, nullable)
- `comment` (text)
- `created_at` (timestamp)

### **Tabel `events`**
Kolom yang digunakan:
- `id` (Primary Key)
- `title` (string)
- `event_date` (date)

### **Tabel `users`**
Kolom yang digunakan (melalui relasi):
- `id` (Primary Key)
- `name` (string)
- `avatar` (string, nullable)

### **Tabel `event_registrations`**
Kolom yang digunakan (melalui relasi):
- `id` (Primary Key)
- `event_id` (Foreign Key)
- `user_id` (Foreign Key)
- `status` (string)

---

## 🔗 **RELASI MODEL**

### **Feedback Model**
```php
public function user() {
    return $this->belongsTo(User::class);
}

public function event() {
    return $this->belongsTo(Event::class);
}
```

### **Event Model**
```php
public function feedbacks() {
    return $this->hasMany(Feedback::class);
}

public function registrations() {
    return $this->hasMany(EventRegistration::class);
}
```

### **User Model**
```php
// Relasi tidak langsung digunakan di Feedback Analysis
// Tapi diakses melalui Feedback->user
```

---

## 📝 **CATATAN PENTING**

1. **Filter Tanggal:** Semua query dapat difilter berdasarkan `feedback.created_at` menggunakan parameter `date_from` dan `date_to`.

2. **Filter Event:** Query dapat difilter berdasarkan `event_id` untuk menampilkan analisis detail event tertentu.

3. **Nullable Fields:** `speaker_rating` dan `committee_rating` adalah nullable, jadi query menggunakan `whereNotNull()` untuk menghitung rata-rata hanya dari feedback yang memiliki nilai tersebut.

4. **Eager Loading:** Menggunakan `with()` dan `withCount()` untuk menghindari N+1 query problem.

5. **Pagination:** Daftar event menggunakan pagination (15 per halaman).

6. **Aggregation Functions:**
   - `COUNT(*)` untuk menghitung jumlah
   - `AVG(column)` untuk menghitung rata-rata
   - `GROUP BY` untuk distribusi rating

