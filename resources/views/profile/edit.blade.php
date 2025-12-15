<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil - idSPORA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * {
            font-family: 'Inter', 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }
        
        /* Background same as dashboard */
        body {
            background-color: #f8fafc;
            min-height: 100vh;
            padding-top: 70px;
        }
        
        /* Ensure navbar is visible and on top */
        .navbar {
            z-index: 1050 !important;
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            width: 100% !important;
        }
        
        .navbar-gradient {
            background: linear-gradient(90deg, #252346 0%, #5b56ac 100%) !important;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3) !important;
        }
        
        /* Ensure navbar elements are visible */
        .navbar-gradient .navbar-brand,
        .navbar-gradient .nav-link,
        .navbar-gradient .navbar-text {
            color: #fff !important;
        }
        
        .navbar-gradient .nav-link:hover,
        .navbar-gradient .nav-link:focus {
            color: #ffe8b3 !important;
        }
        
        .navbar-gradient .nav-link.active {
            font-weight: 600;
            color: #ffe8b3 !important;
        }
        
        /* Notification and user dropdown */
        #notifBtn,
        #userDropdown {
            color: white !important;
        }
        
        #notifBtn:hover,
        #userDropdown:hover {
            color: #ffe8b3 !important;
        }
        
        /* Search bar */
        .navbar .form-control {
            color: white !important;
        }
        
        .navbar .form-control::placeholder {
            color: rgba(255, 255, 255, 0.7) !important;
        }
        
        /* Ensure navbar container is visible */
        .navbar .container-fluid {
            display: flex !important;
            visibility: visible !important;
        }
        
        /* Ensure all navbar elements are visible */
        .navbar-brand,
        .navbar-nav,
        .navbar-collapse {
            display: flex !important;
            visibility: visible !important;
        }
        
        /* Navbar Responsive */
        @media (max-width: 991px) {
            .navbar-brand {
                margin-left: 15px !important;
            }
            .navbar-brand img {
                max-width: 60px !important;
            }
            .navbar .form-control {
                width: 100% !important;
                margin: 0.5rem 0 !important;
            }
            .navbar-nav {
                flex-direction: column !important;
                width: 100%;
                margin: 0.5rem 0 !important;
            }
            .navbar-nav .nav-item {
                margin: 0.25rem 0 !important;
                width: 100%;
            }
            .navbar-collapse {
                padding: 1rem 0;
            }
            #notifBtn,
            #userDropdown {
                margin: 0.5rem 0 !important;
            }
            .d-flex.align-items-center.ms-3 {
                flex-direction: column !important;
                width: 100%;
                margin-right: 0 !important;
                margin-left: 0 !important;
            }
        }
        
        /* Card styling with light background */
        .glass-card {
            background: white;
            border: 1px solid #e5e7eb;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border-radius: 0.75rem;
        }
        
        .glass-sidebar {
            background: white;
            border-right: 1px solid #e5e7eb;
            position: fixed;
            top: 70px;
            left: 0;
            height: calc(100vh - 70px);
            overflow-y: auto;
            z-index: 1000;
            width: 280px;
        }
        
        /* Main content with sidebar offset */
        .main-content-with-sidebar {
            margin-left: 280px;
            padding: 2rem;
        }
        
        /* Responsive Design */
        @media (max-width: 1024px) {
            body {
                padding-top: 70px;
            }
            .flex.min-h-screen {
                flex-direction: column;
            }
            .glass-sidebar {
                position: relative;
                width: 100%;
                height: auto;
                top: 0;
                border-right: none;
                border-bottom: 1px solid #e5e7eb;
                padding: 1.25rem;
            }
            .main-content-with-sidebar {
                margin-left: 0;
                margin-top: 0;
                padding: 1.5rem;
            }
            .search-bar {
                margin-bottom: 1rem;
            }
        }
        
        @media (max-width: 768px) {
            body {
                padding-top: 70px;
            }
            .flex.min-h-screen {
                flex-direction: column;
            }
            .glass-sidebar {
                width: 100%;
                padding: 1rem;
            }
            .glass-sidebar .search-bar {
                margin-bottom: 1rem;
            }
            .main-content-with-sidebar {
                padding: 1rem;
            }
            .glass-card {
                padding: 1.5rem !important;
            }
            .glass-card h1 {
                font-size: 1.75rem !important;
            }
            .profile-img-wrapper img {
                width: 80px !important;
                height: 80px !important;
            }
            .flex.items-center.space-x-4 {
                flex-direction: column;
                align-items: flex-start;
            }
            .flex.items-center.space-x-4 > * {
                margin-bottom: 1rem;
            }
            .grid {
                grid-template-columns: 1fr !important;
                gap: 1rem !important;
            }
            .menu-item {
                padding: 0.75rem !important;
            }
            .menu-item .w-10 {
                width: 2rem !important;
                height: 2rem !important;
            }
            .menu-item .font-semibold {
                font-size: 0.875rem !important;
            }
            .menu-item .text-xs {
                font-size: 0.75rem !important;
            }
            .neu-input {
                font-size: 16px !important; /* Prevent zoom on iOS */
            }
        }
        
        @media (max-width: 480px) {
            .glass-card {
                padding: 1rem !important;
            }
            .glass-card h1 {
                font-size: 1.5rem !important;
            }
            .profile-img-wrapper img {
                width: 60px !important;
                height: 60px !important;
            }
            .premium-badge {
                font-size: 0.625rem !important;
                padding: 0.25rem 0.75rem !important;
            }
            .gold-accent {
                width: 100%;
                justify-content: center;
            }
        }
        
        /* Sidebar Menu Item Hover */
        .menu-item {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            color: #374151;
        }
        
        .menu-item:hover {
            background: #f9fafb;
            transform: translateX(4px);
        }
        
        .menu-item.active {
            background: #fef3c7;
            border-left: 3px solid #fbbf24;
        }
        
        .menu-item .font-semibold {
            color: #111827;
        }
        
        .menu-item .text-gray-400 {
            color: #6b7280;
        }
        
        /* Search Bar - Minimalist */
        .search-bar {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            transition: all 0.2s ease;
        }
        
        .search-bar:focus-within {
            background: white;
            border-color: #d1d5db;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }
        
        .search-bar input {
            color: #111827;
            font-size: 14px;
        }
        
        .search-bar input::placeholder {
            color: #9ca3af;
        }
        
        .search-bar i {
            color: #6b7280;
            font-size: 14px;
        }
        
        /* Input styling with light theme */
        .neu-input {
            background: white;
            border: 1px solid #d1d5db;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }
        
        .neu-input:focus {
            border-color: #fbbf24;
            box-shadow: 0 0 0 3px rgba(251, 191, 36, 0.1);
            outline: none;
        }
        
        /* Premium Gold Accent */
        .gold-accent {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            box-shadow: 0 4px 15px rgba(251, 191, 36, 0.3);
        }
        
        .gold-accent:hover {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            box-shadow: 0 6px 20px rgba(251, 191, 36, 0.4);
            transform: translateY(-1px);
        }
        
        /* Profile Image Overlay */
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
        
        /* Smooth Animations */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .fade-in {
            animation: fadeIn 0.5s ease-out;
        }
    </style>
</head>
<body>
    @include("partials.navbar-after-login")
    
    <div class="flex min-h-screen">
        <!-- Sidebar - Minimalist & Professional -->
        <aside class="glass-sidebar p-5 flex flex-col">
            <!-- Search Bar -->
            <div class="search-bar px-3 py-2.5 flex items-center space-x-2.5 mb-6">
                <i class="bi bi-search menu-icon"></i>
                <input 
                    type="text" 
                    placeholder="Cari..." 
                    class="bg-transparent border-none outline-none flex-1"
                >
            </div>
            
            <!-- Menu Items -->
            <nav class="flex-1">
                <a href="{{ route('profile.index') }}" class="menu-item flex items-center justify-between px-3 py-2.5 group {{ request()->routeIs('profile.index') || request()->routeIs('profile.edit') ? 'active' : '' }}">
                    <div class="flex items-center space-x-3">
                        <i class="bi bi-person menu-icon"></i>
                        <div>
                            <div class="font-semibold">Profile</div>
                            <div class="text-xs">Informasi pribadi</div>
                        </div>
                    </div>
                    <i class="bi bi-chevron-right menu-chevron"></i>
                </a>
                
                <a href="{{ route('profile.events') }}" class="menu-item flex items-center justify-between px-3 py-2.5 group {{ request()->routeIs('profile.events') ? 'active' : '' }}">
                    <div class="flex items-center space-x-3">
                        <i class="bi bi-calendar-check menu-icon"></i>
                        <div>
                            <div class="font-semibold">History Event</div>
                            <div class="text-xs">Event yang diikuti</div>
                        </div>
                    </div>
                    <i class="bi bi-chevron-right menu-chevron"></i>
                </a>
            </nav>
        </aside>
        
        <!-- Main Content -->
        <main class="main-content-with-sidebar flex-1 overflow-y-auto" style="margin-top: 70px;">
            <div class="max-w-4xl mx-auto fade-in">
            <!-- Header -->
            <div class="mb-6">
                <h1 class="text-3xl font-bold mb-2" style="color: #111827;">Edit Profil</h1>
                <p class="text-sm" style="color: #6b7280;">Perbarui informasi profil Anda</p>
            </div>
            
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
                    <div>
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
                    <div>
                        <label class="block text-sm font-semibold mb-2" style="color: #374151;">
                            Nama Lengkap <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="name" 
                            value="{{ old('name', $user->name) }}"
                            class="neu-input w-full px-4 py-3 rounded-xl focus:outline-none transition-all"
                            style="color: #111827;"
                            placeholder="Masukkan nama lengkap"
                            required
                        >
                    </div>
                    
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
                    
                    <!-- Phone -->
                    <div>
                        <label class="block text-sm font-semibold mb-2" style="color: #374151;">
                            Nomor Telepon
                        </label>
                        <input 
                            type="tel" 
                            name="phone" 
                            value="{{ old('phone', $user->phone ?? '') }}"
                            class="neu-input w-full px-4 py-3 rounded-xl focus:outline-none transition-all"
                            style="color: #111827;"
                            placeholder="+62 812 3456 7890"
                        >
                    </div>
                    
                    <!-- Website -->
                    <div>
                        <label class="block text-sm font-semibold mb-2" style="color: #374151;">
                            Website/Portofolio
                        </label>
                        <input 
                            type="url" 
                            name="website" 
                            value="{{ old('website', $user->website ?? '') }}"
                            class="neu-input w-full px-4 py-3 rounded-xl focus:outline-none transition-all"
                            style="color: #111827;"
                            placeholder="https://yourwebsite.com"
                        >
                    </div>
                    
                    <!-- Bio -->
                    <div>
                        <label class="block text-sm font-semibold mb-2" style="color: #374151;">
                            Bio
                        </label>
                        <textarea 
                            name="bio"
                            rows="4"
                            class="neu-input w-full px-4 py-3 rounded-xl focus:outline-none resize-none transition-all"
                            style="color: #111827;"
                            placeholder="Ceritakan tentang diri Anda..."
                        >{{ old('bio', $user->bio ?? '') }}</textarea>
                    </div>
                    
                    <!-- Password Fields -->
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold mb-2" style="color: #374151;">
                                Password Baru (opsional)
                            </label>
                            <input 
                                type="password" 
                                name="password" 
                                class="neu-input w-full px-4 py-3 rounded-xl focus:outline-none transition-all"
                                style="color: #111827;"
                                placeholder="Biarkan kosong jika tidak diganti"
                            >
                        </div>
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
                            href="{{ route('profile.index') }}"
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
        </main>
    </div>
    
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
    </script>
</body>
</html>

