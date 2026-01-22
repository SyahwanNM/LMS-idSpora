@php
    $user = Auth::user();
@endphp
<!-- Header -->
<div class="mb-6">
    <h1 class="text-3xl font-bold mb-2" style="color: #111827;">Pengaturan Akun</h1>
    <p class="text-sm" style="color: #6b7280;">Kelola email dan password akun Anda</p>
</div>

<!-- Account Settings Card -->
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
    
    <!-- Account Settings Form -->
    <form action="{{ route('profile.update-account-settings') }}" method="POST" class="space-y-6">
        @csrf
        
        <!-- Email -->
        <div>
            <label class="block text-sm font-semibold mb-2" style="color: #374151;">
                Email <span class="text-red-500">*</span>
            </label>
            <input 
                type="email" 
                name="email" 
                value="{{ old('email', $user->email) }}"
                class="neu-input w-full px-4 py-3 rounded-xl focus:outline-none transition-all"
                style="color: #111827;"
                placeholder="nama@email.com"
                required
            >
        </div>
        
        <!-- Password Section -->
        <div class="pt-4 border-t border-gray-200">
            <h3 class="text-lg font-semibold mb-4" style="color: #111827;">Ubah Password</h3>
            <p class="text-sm mb-4" style="color: #6b7280;">Biarkan kosong jika tidak ingin mengubah password</p>
            
            <div class="space-y-4">
                <!-- Current Password -->
                <div>
                    <label class="block text-sm font-semibold mb-2" style="color: #374151;">
                        Password Saat Ini
                    </label>
                    <input 
                        type="password" 
                        name="current_password" 
                        class="neu-input w-full px-4 py-3 rounded-xl focus:outline-none transition-all"
                        style="color: #111827;"
                        placeholder="Masukkan password saat ini"
                    >
                </div>
                
                <!-- New Password -->
                <div>
                    <label class="block text-sm font-semibold mb-2" style="color: #374151;">
                        Password Baru
                    </label>
                    <input 
                        type="password" 
                        name="password" 
                        class="neu-input w-full px-4 py-3 rounded-xl focus:outline-none transition-all"
                        style="color: #111827;"
                        placeholder="Masukkan password baru (min. 6 karakter)"
                    >
                </div>
                
                <!-- Confirm Password -->
                <div>
                    <label class="block text-sm font-semibold mb-2" style="color: #374151;">
                        Konfirmasi Password Baru
                    </label>
                    <input 
                        type="password" 
                        name="password_confirmation" 
                        class="neu-input w-full px-4 py-3 rounded-xl focus:outline-none transition-all"
                        style="color: #111827;"
                        placeholder="Ulangi password baru"
                    >
                </div>
            </div>
        </div>
        
        <!-- Submit Button -->
        <div class="flex justify-end pt-4">
            <button 
                type="submit" 
                class="gold-accent px-6 py-3 rounded-xl text-sm font-semibold"
            >
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
