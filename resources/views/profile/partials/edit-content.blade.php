@php
    $user = Auth::user();
@endphp
<!-- Profile Information Card -->
<div class="glass-card rounded-2xl p-8 shadow-2xl">
    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200">
            <p class="text-green-800 text-sm font-medium">{{ session('success') }}</p>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200">
            <ul class="list-disc list-inside space-y-1 text-red-800 text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    <!-- Edit Profile Form -->
    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        
        <!-- Avatar Upload -->
        <div id="field-avatar">
            <label class="block text-sm font-semibold mb-2" style="color: #374151;">
                Foto Profil
            </label>
            <div class="flex items-center space-x-4">
                <div class="profile-img-wrapper">
                    <img 
                        id="avatarPreview"
                        src="{{ $user->avatar_url }}" 
                        alt="Profile" 
                        class="w-24 h-24 rounded-full object-cover border-4 border-yellow-400 shadow-lg"
                        referrerpolicy="no-referrer"
                        onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=fbbf24&color=1e1b4b&size=128';"
                    >
                    <div class="profile-img-overlay" onclick="document.getElementById('avatarInput').click()">
                        <i class="bi bi-camera text-white text-lg"></i>
                    </div>
                </div>
                <div class="flex-1">
                    <input 
                        type="file" 
                        name="avatar" 
                        id="avatarInput"
                        accept="image/*"
                        class="hidden"
                        onchange="previewAvatar(this)"
                    >
                    <label for="avatarInput" class="cursor-pointer">
                        <span class="px-4 py-2 rounded-lg border-2 font-semibold transition-all duration-300 inline-block" style="border-color: #d1d5db; color: #374151;" onmouseover="this.style.backgroundColor='#f9fafb'" onmouseout="this.style.backgroundColor='transparent'">
                            <i class="bi bi-upload mr-2"></i>Unggah Foto
                        </span>
                    </label>
                    <p class="text-xs mt-2" style="color: #6b7280;">Format: JPG, PNG, atau WEBP. Maksimal 4MB.</p>
                </div>
            </div>
        </div>
        
        <!-- Name -->
        <div id="field-name">
            <label class="block text-sm font-semibold mb-2" style="color: #374151;">
                Nama Lengkap <span class="text-red-500">*</span>
            </label>
            <input 
                type="text" 
                name="name" 
                id="input-name"
                value="{{ old('name', $user->name) }}"
                class="neu-input w-full px-4 py-3 rounded-xl focus:outline-none transition-all"
                style="color: #111827;"
                placeholder="Masukkan nama lengkap"
                required
            >
        </div>
        
        <!-- Phone -->
        <div id="field-phone">
            <label class="block text-sm font-semibold mb-2" style="color: #374151;">
                Nomor Telepon <span class="text-red-500">*</span>
            </label>
            <div class="flex gap-3">
                <!-- Country Code Dropdown -->
                <div style="flex-shrink: 0; width: 150px;" class="country-code-wrapper">
                    <select 
                        name="phone_country_code" 
                        id="phone-country-code"
                        class="neu-input country-code-select w-full rounded-xl focus:outline-none transition-all @error('phone') border-red-300 @enderror"
                        style="color: #111827;"
                    >
                        <option value="+62" {{ old('phone_country_code', $user->phone_country_code ?? '+62') == '+62' ? 'selected' : '' }}>🇮🇩 +62 (ID)</option>
                        <option value="+60" {{ old('phone_country_code', $user->phone_country_code ?? '') == '+60' ? 'selected' : '' }}>🇲🇾 +60 (MY)</option>
                        <option value="+65" {{ old('phone_country_code', $user->phone_country_code ?? '') == '+65' ? 'selected' : '' }}>🇸🇬 +65 (SG)</option>
                        <option value="+1" {{ old('phone_country_code', $user->phone_country_code ?? '') == '+1' ? 'selected' : '' }}>🇺🇸 +1 (US)</option>
                        <option value="+44" {{ old('phone_country_code', $user->phone_country_code ?? '') == '+44' ? 'selected' : '' }}>🇬🇧 +44 (GB)</option>
                        <option value="+61" {{ old('phone_country_code', $user->phone_country_code ?? '') == '+61' ? 'selected' : '' }}>🇦🇺 +61 (AU)</option>
                        <option value="+86" {{ old('phone_country_code', $user->phone_country_code ?? '') == '+86' ? 'selected' : '' }}>🇨🇳 +86 (CN)</option>
                        <option value="+81" {{ old('phone_country_code', $user->phone_country_code ?? '') == '+81' ? 'selected' : '' }}>🇯🇵 +81 (JP)</option>
                        <option value="+82" {{ old('phone_country_code', $user->phone_country_code ?? '') == '+82' ? 'selected' : '' }}>🇰🇷 +82 (KR)</option>
                        <option value="+66" {{ old('phone_country_code', $user->phone_country_code ?? '') == '+66' ? 'selected' : '' }}>🇹🇭 +66 (TH)</option>
                        <option value="+84" {{ old('phone_country_code', $user->phone_country_code ?? '') == '+84' ? 'selected' : '' }}>🇻🇳 +84 (VN)</option>
                        <option value="+63" {{ old('phone_country_code', $user->phone_country_code ?? '') == '+63' ? 'selected' : '' }}>🇵🇭 +63 (PH)</option>
                        <option value="+91" {{ old('phone_country_code', $user->phone_country_code ?? '') == '+91' ? 'selected' : '' }}>🇮🇳 +91 (IN)</option>
                    </select>
                </div>
                <!-- Phone Number Input -->
                <div style="flex: 1;">
                    <input 
                        type="tel" 
                        name="phone_number" 
                        id="input-phone"
                        value="{{ old('phone_number', $user->phone_number ?? '') }}"
                        class="neu-input w-full px-4 py-3 rounded-xl focus:outline-none transition-all @error('phone') border-red-300 @enderror"
                        style="color: #111827;"
                        placeholder="812 3456 7890"
                        maxlength="15"
                        inputmode="numeric"
                    >
                </div>
            </div>
            @error('phone')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
            <p class="text-xs mt-2" style="color: #6b7280;">
                <i class="bi bi-info-circle me-1"></i>
                Masukkan nomor tanpa kode negara (contoh: <strong>812 3456 7890</strong> untuk Indonesia)
            </p>
        </div>
        
        <!-- Institution (Instansi) -->
        <div id="field-institution">
            <label class="block text-sm font-semibold mb-2" style="color: #374151;">
                Instansi
            </label>
            <input 
                type="text" 
                name="institution" 
                id="input-institution"
                value="{{ old('institution', $user->institution ?? '') }}"
                class="neu-input w-full px-4 py-3 rounded-xl focus:outline-none transition-all"
                style="color: #111827;"
                placeholder="Masukkan nama instansi/perusahaan/sekolah"
            >
        </div>

        <!-- Profession -->
        <div id="field-profession">
            <label class="block text-sm font-semibold mb-2" style="color: #374151;">
                Profesi
            </label>
            <select
                name="profession"
                id="input-profession"
                class="neu-input w-full px-4 py-3 rounded-xl focus:outline-none transition-all"
                style="color: #111827;"
            >
                <option value="" {{ old('profession', $user->profession ?? '') === '' ? 'selected' : '' }}>Pilih Profesi</option>
                <option value="Pelajar/Mahasiswa" {{ old('profession', $user->profession ?? '') === 'Pelajar/Mahasiswa' ? 'selected' : '' }}>Pelajar/Mahasiswa</option>
                <option value="Karyawan Swasta" {{ old('profession', $user->profession ?? '') === 'Karyawan Swasta' ? 'selected' : '' }}>Karyawan Swasta</option>
                <option value="ASN/PNS" {{ old('profession', $user->profession ?? '') === 'ASN/PNS' ? 'selected' : '' }}>ASN/PNS</option>
                <option value="Wirausaha" {{ old('profession', $user->profession ?? '') === 'Wirausaha' ? 'selected' : '' }}>Wirausaha</option>
                <option value="Lainnya" {{ old('profession', $user->profession ?? '') === 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
            </select>
        </div>

        <!-- Bio -->
        <div id="field-bio">
            <label class="block text-sm font-semibold mb-2" style="color: #374151;">
                Bio
            </label>
            <textarea 
                name="bio"
                id="input-bio"
                rows="4"
                class="neu-input w-full px-4 py-3 rounded-xl focus:outline-none resize-none transition-all"
                style="color: #111827;"
                placeholder="Ceritakan tentang diri Anda..."
            >{{ old('bio', $user->bio ?? '') }}</textarea>
        </div>
        
        <!-- Role Display (Read-only) -->
        <div>
            <label class="block text-sm font-semibold mb-2" style="color: #374151;">
                Role
            </label>
            <div class="neu-input w-full px-4 py-3 rounded-xl" style="color: #6b7280; background: #f9fafb;">
                {{ ucfirst($user->role ?? 'user') }}
            </div>
            <p class="text-xs mt-1" style="color: #9ca3af;">Role tidak dapat diubah</p>
        </div>
        
        <!-- Action Buttons -->
        <div class="flex justify-end space-x-4 pt-6 border-t" style="border-color: #e5e7eb;">
            <a 
                href="{{ route('profile.settings') }}"
                class="px-6 py-3 rounded-xl border-2 font-semibold transition-all duration-300"
                style="border-color: #d1d5db; color: #374151; text-decoration: none;"
                onmouseover="this.style.backgroundColor='#f9fafb'"
                onmouseout="this.style.backgroundColor='transparent'"
            >
                Batal
            </a>
            <button 
                type="submit"
                class="gold-accent px-6 py-3 rounded-xl text-gray-900 font-semibold flex items-center space-x-2 transition-all duration-300"
            >
                <i class="bi bi-save"></i>
                <span>Simpan Perubahan</span>
            </button>
        </div>
    </form>
</div>

<style>
    .profile-img-wrapper {
        position: relative;
        display: inline-block;
    }
    
    .profile-img-overlay {
        position: absolute;
        bottom: 0;
        right: 0;
        background: rgba(251, 191, 36, 0.9);
        border-radius: 50%;
        padding: 8px;
        cursor: pointer;
        transition: all 0.3s;
        border: 2px solid #fbbf24;
    }
    
    .profile-img-overlay:hover {
        background: rgba(234, 179, 8, 0.9);
        transform: scale(1.1);
    }
    
    .country-code-wrapper {
        position: relative;
    }

    .country-code-select {
        padding: 0.75rem 2.5rem 0.75rem 1rem !important;
        font-size: 0.9375rem;
        font-weight: 500;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12' fill='none'%3E%3Cpath d='M2 4L6 8L10 4' stroke='%23374151' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 0.875rem center;
        background-size: 12px;
        cursor: pointer;
    }

    .country-code-select:focus {
        border-color: #fbbf24;
        box-shadow: 0 0 0 3px rgba(251, 191, 36, 0.1);
        outline: none;
    }
</style>

<script>
    // Avatar preview function
    function previewAvatar(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('avatarPreview').src = e.target.result;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Phone number formatting
    document.addEventListener('DOMContentLoaded', function() {
        const phoneInput = document.getElementById('input-phone');
        
        if (phoneInput) {
            // Format phone number saat input (hanya angka dengan spasi untuk readability)
            phoneInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/[^0-9]/g, '');
                
                // Format dengan spasi untuk readability (3-4-4 pattern)
                let formatted = value.replace(/(\d{3})(\d{4})(\d{0,4})/, function(match, p1, p2, p3) {
                    if (p3) {
                        return p1 + ' ' + p2 + ' ' + p3;
                    } else if (p2) {
                        return p1 + ' ' + p2;
                    }
                    return p1;
                });
                
                e.target.value = formatted;
            });
            
            // Hapus leading zero saat blur
            phoneInput.addEventListener('blur', function(e) {
                let value = e.target.value.replace(/[^0-9]/g, '');
                value = value.replace(/^0+/, ''); // Hapus leading zero
                
                // Format ulang
                let formatted = value.replace(/(\d{3})(\d{4})(\d{0,4})/, function(match, p1, p2, p3) {
                    if (p3) {
                        return p1 + ' ' + p2 + ' ' + p3;
                    } else if (p2) {
                        return p1 + ' ' + p2;
                    }
                    return p1;
                });
                
                e.target.value = formatted;
            });
        }
    });
</script>
