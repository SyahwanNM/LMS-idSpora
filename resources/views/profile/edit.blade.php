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
            background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
            border-right: none;
            position: fixed;
            top: 70px;
            left: 0;
            height: calc(100vh - 70px);
            overflow-y: auto;
            z-index: 1000;
            width: 280px;
        }
        
        .sidebar-header {
            text-align: center;
            padding: 1.5rem 1rem;
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            border-bottom: 1px solid rgba(255, 255, 255, 0.15);
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
                border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            }
            .main-content-with-sidebar {
                margin-left: 0;
                margin-top: 0;
                padding: 1.5rem;
            }
        }
        
        @media (max-width: 768px) {
            .glass-card {
                padding: 1.5rem !important;
            }
            
            .glass-card h1 {
                font-size: 1.75rem !important;
            }
            
            .profile-img-wrapper {
                width: 100%;
                display: flex;
                justify-content: center;
                margin-bottom: 1rem;
            }
            
            .flex.items-start.space-x-6 {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }
            
            .flex.items-start.space-x-6 > * {
                margin-bottom: 1rem;
            }
            
            .country-code-wrapper {
                width: 100% !important;
            }
            
            .flex.gap-3 {
                flex-direction: column;
                gap: 0.75rem !important;
            }
            
            #input-phone {
                width: 100%;
            }
        }
        
        @media (max-width: 576px) {
            .glass-card {
                padding: 1rem !important;
            }
            
            .glass-card h1 {
                font-size: 1.5rem !important;
            }
            
            .country-code-select {
                font-size: 0.875rem;
                padding: 0.65rem 2.25rem 0.65rem 0.875rem !important;
            }
            
            .neu-input {
                font-size: 0.9rem;
                padding: 0.75rem 1rem !important;
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
        
        /* Sidebar Menu Item - Matching Navbar Gradient */
        .menu-item {
            transition: all 0.3s ease;
            color: rgba(255, 255, 255, 0.7);
            border-radius: 12px;
            margin: 0.5rem 1rem;
            padding: 0.875rem 1rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            position: relative;
        }
        
        .menu-item:hover {
            color: rgba(255, 255, 255, 0.9);
            background: rgba(255, 255, 255, 0.1);
        }
        
        .menu-item.active {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            color: #1e1b4b;
            box-shadow: 0 2px 8px rgba(251, 191, 36, 0.4), inset 0 1px 0 rgba(255, 255, 255, 0.2);
        }
        
        .menu-item.active .menu-icon {
            color: #1e1b4b;
        }
        
        .menu-item .menu-text {
            color: inherit;
            font-size: 14px;
            font-weight: 500;
        }
        
        .menu-icon {
            color: rgba(255, 255, 255, 0.7);
            font-size: 20px;
            transition: color 0.3s ease;
            width: 24px;
            text-align: center;
        }
        
        .menu-item:hover .menu-icon {
            color: rgba(255, 255, 255, 0.9);
        }
        
        .menu-item.active .menu-icon {
            color: white;
        }
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

        /* Country Code Dropdown Styling */
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

        .country-code-select option {
            padding: 0.5rem;
            font-size: 0.9375rem;
            font-weight: 500;
        }

        @media (max-width: 768px) {
            .country-code-wrapper {
                width: 100% !important;
            }
        }
    </style>
</head>
<body>
    @include("partials.navbar-after-login")
    
    <div class="flex min-h-screen">
        <!-- Sidebar - Dark Theme with Purple Gradient Active -->
        <aside class="glass-sidebar flex flex-col">
            <!-- Sidebar Header -->
            <div class="sidebar-header">
                MENU NAVIGASI
            </div>
            
            <!-- Menu Items -->
            <nav class="flex-1 py-4">
                <a href="{{ route('profile.index') }}" class="menu-item {{ request()->routeIs('profile.index') || request()->routeIs('profile.edit') ? 'active' : '' }}">
                    <i class="bi bi-person menu-icon"></i>
                    <span class="menu-text">Profile</span>
                </a>
                
                <a href="{{ route('profile.events') }}" class="menu-item {{ request()->routeIs('profile.events') ? 'active' : '' }}">
                    <i class="bi bi-calendar-check menu-icon"></i>
                    <span class="menu-text">History Event</span>
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
                    
                    <!-- Email -->
                    <div id="field-email">
                        <label class="block text-sm font-semibold mb-2" style="color: #374151;">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="email" 
                            name="email" 
                            id="input-email"
                            value="{{ old('email', $user->email) }}"
                            class="neu-input w-full px-4 py-3 rounded-xl focus:outline-none transition-all"
                            style="color: #111827;"
                            placeholder="nama@email.com"
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

        // Phone number formatting
        document.addEventListener('DOMContentLoaded', function() {
            const phoneInput = document.getElementById('input-phone');
            const countryCodeSelect = document.getElementById('phone-country-code');
            
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

        // Deep-link to field based on query parameter
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const focusField = urlParams.get('focus');
            
            if (focusField) {
                // Map field names to input IDs
                const fieldMap = {
                    'name': 'input-name',
                    'email': 'input-email',
                    'phone': 'input-phone',
                    'avatar': 'avatarInput',
                    'bio': 'input-bio'
                };
                
                const inputId = fieldMap[focusField];
                if (inputId) {
                    const inputElement = document.getElementById(inputId);
                    if (inputElement) {
                        // Scroll to field
                        const fieldDiv = document.getElementById('field-' + focusField);
                        if (fieldDiv) {
                            fieldDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }
                        
                        // Focus on input after a short delay
                        setTimeout(function() {
                            inputElement.focus();
                            
                            // Highlight the field
                            inputElement.style.border = '2px solid #fbbf24';
                            inputElement.style.boxShadow = '0 0 0 3px rgba(251, 191, 36, 0.2)';
                            
                            // Remove highlight after 3 seconds
                            setTimeout(function() {
                                inputElement.style.border = '';
                                inputElement.style.boxShadow = '';
                            }, 3000);
                        }, 300);
                    }
                }
            }
        });
    </script>
</body>
</html>
