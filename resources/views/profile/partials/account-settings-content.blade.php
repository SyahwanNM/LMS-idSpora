@php
    $user = Auth::user();
@endphp
<!-- Account Settings Card -->
<div class="glass-card rounded-2xl p-8 shadow-2xl" style="border-radius: 28px; box-shadow: 0 18px 50px rgba(15, 23, 42, 0.08);">
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
    <form action="{{ route('profile.update-account-settings') }}" method="POST" class="space-y-8">
        @csrf

        <!-- Email Section -->
        <section>
            <div class="flex items-center gap-3 mb-4">
                <div class="flex items-center justify-center" style="width:44px; height:44px; border-radius: 14px; background:#eef2ff; color:#4f46e5;">
                    <i class="bi bi-envelope"></i>
                </div>
                <h3 class="text-xl font-bold mb-0" style="color:#0f172a;">Informasi Email</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <div class="text-xs font-bold uppercase tracking-widest mb-2" style="color:#94a3b8;">Email Saat Ini</div>
                    <input
                        type="email"
                        value="{{ $user->email }}"
                        class="neu-input w-full px-4 py-3 rounded-2xl focus:outline-none transition-all"
                        style="color:#64748b; background:#f8fafc;"
                        disabled
                    >
                </div>

                <div>
                    <div class="text-xs font-bold uppercase tracking-widest mb-2" style="color:#94a3b8;">Email Baru</div>
                    <input
                        type="email"
                        name="new_email"
                        value="{{ old('new_email', '') }}"
                        class="neu-input w-full px-4 py-3 rounded-2xl focus:outline-none transition-all"
                        style="color:#111827;"
                        placeholder="Masukkan email baru..."
                        autocomplete="email"
                    >
                </div>
            </div>

            <div class="mt-4 p-4 rounded-2xl" style="background:#fffbeb; border:1px solid #fde68a;">
                <div class="flex items-start gap-3">
                    <i class="bi bi-exclamation-triangle-fill" style="color:#f59e0b; font-size:1.1rem; margin-top:2px;"></i>
                    <p class="text-sm mb-0" style="color:#92400e; line-height:1.6;">
                        <b>PENTING:</b> Mengubah email akan mengharuskan Anda untuk melakukan verifikasi ulang akun.
                        Link konfirmasi akan dikirimkan ke email baru Anda.
                    </p>
                </div>
            </div>
        </section>
        
        <!-- Password Section -->
        <section class="pt-2">
            <div class="flex items-center gap-3 mb-4">
                <div class="flex items-center justify-center" style="width:44px; height:44px; border-radius: 14px; background:#fff1f2; color:#ef4444;">
                    <i class="bi bi-lock"></i>
                </div>
                <h3 class="text-xl font-bold mb-0" style="color:#0f172a;">Ganti Password</h3>
            </div>

            <div class="space-y-5">
                <div>
                    <div class="text-xs font-bold uppercase tracking-widest mb-2" style="color:#94a3b8;">Password Sekarang</div>
                    <div class="relative">
                        <input
                            type="password"
                            name="current_password"
                            id="currentPassword"
                            class="neu-input w-full px-4 py-3 rounded-2xl focus:outline-none transition-all"
                            style="color:#111827; padding-right: 3rem;"
                            placeholder="********"
                            autocomplete="current-password"
                        >
                        <button type="button"
                                class="absolute top-1/2 -translate-y-1/2"
                                style="right: 0.75rem; width: 36px; height: 36px; border-radius: 12px; border: none; background: transparent; color:#94a3b8;"
                                onclick="togglePassword('currentPassword', this)">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                <div>
                    <div class="text-xs font-bold uppercase tracking-widest mb-2" style="color:#94a3b8;">Password Baru</div>
                    <input
                        type="password"
                        name="password"
                        id="newPassword"
                        class="neu-input w-full px-4 py-3 rounded-2xl focus:outline-none transition-all"
                        style="color:#111827;"
                        placeholder="Minimal 6 karakter"
                        autocomplete="new-password"
                    >
                </div>

                <div>
                    <div class="text-xs font-bold uppercase tracking-widest mb-2" style="color:#94a3b8;">Konfirmasi Password Baru</div>
                    <input
                        type="password"
                        name="password_confirmation"
                        id="confirmNewPassword"
                        class="neu-input w-full px-4 py-3 rounded-2xl focus:outline-none transition-all"
                        style="color:#111827;"
                        placeholder="Ulangi password baru"
                        autocomplete="new-password"
                    >
                </div>
            </div>
        </section>
        
        <!-- Submit Button -->
        <div class="pt-2 flex items-center justify-between">
            <a href="{{ route('profile.account-settings') }}"
               class="text-sm font-bold tracking-widest"
               style="color:#94a3b8; text-decoration:none;"
               onmouseover="this.style.color='#64748b'"
               onmouseout="this.style.color='#94a3b8'">
                BATALKAN
            </a>

            <button type="submit"
                    class="px-6 py-3 rounded-2xl text-white font-extrabold tracking-widest inline-flex items-center gap-2"
                    style="background:#4f46e5; border:none; box-shadow: 0 10px 24px rgba(79, 70, 229, 0.25);">
                <i class="bi bi-save"></i>
                SIMPAN PERUBAHAN
            </button>
        </div>
    </form>
</div>

<script>
    function togglePassword(inputId, btn) {
        const input = document.getElementById(inputId);
        if (!input) return;
        const icon = btn ? btn.querySelector('i') : null;
        const isPassword = input.type === 'password';
        input.type = isPassword ? 'text' : 'password';
        if (icon) {
            icon.className = isPassword ? 'bi bi-eye-slash' : 'bi bi-eye';
        }
    }
</script>
