# Arsitektur Fitur In-App Profile Reminder

## 1. Desain Arsitektur

### Komponen Utama:
1. **Model Layer**
   - `User` - Model user dengan method untuk menghitung profile completion
   - `ProfileReminder` - Model untuk tracking reminder history

2. **Service Layer**
   - `ProfileReminderService` - Business logic untuk reminder (check, dismiss, activate/deactivate)

3. **Controller Layer**
   - `ProfileReminderController` - API endpoint untuk check dan dismiss reminder
   - `ProfileController` - Auto-deactivate reminder setelah profile update

4. **View Layer**
   - `partials/profile-reminder-banner.blade.php` - Banner component dengan progress bar
   - `profile/edit.blade.php` - Deep-link support untuk auto-focus field

### Flow:
```
User Login/Register
    ↓
Banner Component Load (via navbar-after-login)
    ↓
AJAX Check Reminder Status (GET /api/profile-reminder/check)
    ↓
ProfileReminderService.shouldShowReminder()
    ↓
Jika perlu: Tampilkan Banner dengan Progress Bar
    ↓
User Klik "Lengkapi Sekarang" → Deep-link ke profile/edit?focus=field
    ↓
User Update Profile → Auto-deactivate reminder jika ≥80%
```

## 2. Skema Database

### Tabel: `profile_reminders`
```sql
CREATE TABLE profile_reminders (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    last_shown_at TIMESTAMP NULL COMMENT 'Timestamp terakhir reminder ditampilkan',
    dismiss_count INT DEFAULT 0 COMMENT 'Jumlah kali user dismiss reminder (maksimal 2)',
    is_active BOOLEAN DEFAULT TRUE COMMENT 'Apakah reminder masih aktif',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_active (user_id, is_active)
);
```

## 3. API Endpoints

### GET `/api/profile-reminder/check`
**Deskripsi:** Cek status reminder untuk user yang sedang login

**Response Success (200):**
```json
{
    "should_show": true,
    "completion_percentage": 60,
    "missing_fields": ["phone", "avatar", "bio"],
    "first_missing_field": "phone",
    "is_complete": false
}
```

**Response Not Needed (200):**
```json
{
    "should_show": false,
    "message": "Reminder tidak perlu ditampilkan"
}
```

### POST `/api/profile-reminder/dismiss`
**Deskripsi:** Dismiss reminder (user menutup reminder)

**Response Success (200):**
```json
{
    "success": true,
    "message": "Reminder dismissed"
}
```

## 4. Pseudocode Logic Reminder

### Profile Completion Calculation
```
FUNCTION getProfileCompletionPercentage(user):
    fields = {
        name: user.name is not empty,
        email: user.email is not empty,
        phone: user.phone is not empty,
        avatar: user.avatar is not empty,
        bio: user.bio is not empty
    }
    
    completed = COUNT(fields WHERE value is TRUE)
    total = COUNT(fields)
    
    RETURN (completed / total) * 100
END FUNCTION
```

### Should Show Reminder Logic
```
FUNCTION shouldShowReminder(user):
    // Rule 1: Profile harus < 80%
    IF user.isProfileComplete() THEN
        deactivateReminder(user)
        RETURN FALSE
    END IF
    
    reminder = ProfileReminder.findOrCreate(user_id: user.id)
    
    // Rule 2: Maksimal dismiss 2x
    IF reminder.dismiss_count >= 2 THEN
        reminder.is_active = FALSE
        reminder.save()
        RETURN FALSE
    END IF
    
    // Rule 3: Reminder harus aktif
    IF NOT reminder.is_active THEN
        RETURN FALSE
    END IF
    
    // Rule 4: Maksimal 1x per hari
    IF reminder.last_shown_at IS NOT NULL THEN
        lastShown = PARSE_DATE(reminder.last_shown_at)
        today = TODAY()
        
        IF lastShown.date == today.date THEN
            RETURN FALSE
        END IF
    END IF
    
    RETURN TRUE
END FUNCTION
```

### Dismiss Reminder Logic
```
FUNCTION dismissReminder(user):
    reminder = ProfileReminder.findOrCreate(user_id: user.id)
    
    newDismissCount = reminder.dismiss_count + 1
    
    reminder.dismiss_count = newDismissCount
    reminder.is_active = (newDismissCount < 2)
    reminder.save()
END FUNCTION
```

### Mark as Shown Logic
```
FUNCTION markAsShown(user):
    reminder = ProfileReminder.findOrCreate(user_id: user.id)
    
    reminder.last_shown_at = NOW()
    reminder.save()
END FUNCTION
```

## 5. Best Practices

### 1. **Rate Limiting**
- Reminder maksimal 1x per hari untuk menghindari spam
- Dismiss maksimal 2x sebelum reminder nonaktif

### 2. **User Experience**
- Banner dengan progress bar yang jelas menunjukkan completion percentage
- Deep-link langsung ke field yang kosong untuk memudahkan user
- Auto-focus dan highlight field yang perlu dilengkapi
- Smooth animation untuk banner (slide down)

### 3. **Performance**
- Index pada `(user_id, is_active)` untuk query yang cepat
- AJAX check reminder untuk tidak blocking page load
- Lazy loading banner (hanya load jika user authenticated dan bukan admin)

### 4. **Security**
- Middleware `auth` pada semua API endpoint
- CSRF protection pada POST request
- Validasi user ownership (hanya bisa dismiss reminder sendiri)

### 5. **Maintainability**
- Service layer untuk business logic (mudah di-test dan di-reuse)
- Separation of concerns (Model, Service, Controller, View)
- Clear naming convention dan documentation

### 6. **Error Handling**
- Try-catch untuk database operations
- Graceful fallback jika reminder check gagal (banner tidak muncul)
- Console logging untuk debugging

### 7. **Accessibility**
- ARIA labels pada progress bar
- Keyboard navigation support
- Screen reader friendly

## 6. Field Mapping untuk Deep-Link

| Field Name | Input ID | Field Container ID |
|------------|----------|-------------------|
| name | input-name | field-name |
| email | input-email | field-email |
| phone | input-phone | field-phone |
| avatar | avatarInput | field-avatar |
| bio | input-bio | field-bio |

## 7. Profile Completion Threshold

- **Lengkap:** ≥ 80% (4 dari 5 field terisi)
- **Field yang dihitung:**
  1. Name (required)
  2. Email (required)
  3. Phone (required)
  4. Avatar (required)
  5. Bio (soft mandatory)

## 8. Testing Checklist

- [ ] Reminder muncul setelah register jika profile < 80%
- [ ] Reminder muncul saat login jika profile < 80%
- [ ] Reminder tidak muncul jika profile ≥ 80%
- [ ] Reminder tidak muncul jika sudah dismiss 2x
- [ ] Reminder tidak muncul jika sudah ditampilkan hari ini
- [ ] Reminder tidak muncul untuk admin
- [ ] Deep-link berfungsi ke field yang kosong
- [ ] Auto-focus dan highlight field saat deep-link
- [ ] Progress bar menampilkan percentage yang benar
- [ ] Dismiss button berfungsi
- [ ] Reminder auto-deactivate setelah profile update ≥ 80%

