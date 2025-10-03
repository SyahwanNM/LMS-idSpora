@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="content-wrapper min-h-screen bg-gradient-to-br from-amber-50 via-yellow-50 to-orange-50 flex flex-col">
    <!-- Header -->
    <header class="bg-gradient-to-r from-amber-600 to-yellow-500 shadow-lg border-b border-amber-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0">
                        <img src="{{ asset('images/logo idspora_nobg_dark 1.png') }}" alt="idSpora Logo" class="h-12 w-auto">
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-white">Admin Dashboard</h1>
                        <p class="text-amber-100 text-sm">idSpora Learning Management System</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <a href="{{ route('landing-page') }}" class="bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white px-4 py-2 rounded-lg transition-all duration-200 flex items-center space-x-2 border border-white/30">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                            <span>Beranda</span>
                        </a>
                    </div>
                    <div class="relative">
                        <button id="exportDataBtn" class="bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white px-4 py-2 rounded-lg transition-all duration-200 flex items-center space-x-2 border border-white/30" type="button" data-export-url="{{ route('admin.export') }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5-5-5h5v-12"></path>
                            </svg>
                            <span>Export Data</span>
                        </button>
                    </div>
                    <!-- User Dropdown -->
                    <div class="relative">
                        <button id="userDropdownButton" onclick="toggleUserDropdown()" class="flex items-center space-x-3 bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white px-4 py-2 rounded-lg transition-all duration-200 border border-white/30">
                            <img class="h-8 w-8 rounded-full border-2 border-white/30" src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name ?? 'Admin') }}&background=f59e0b&color=fff" alt="Admin">
                            <div class="flex flex-col items-start">
                                <span class="text-sm font-medium">{{ Auth::user()->name ?? 'Admin' }}</span>
                                <span class="text-xs text-amber-100">Administrator</span>
                            </div>
                            <svg class="w-4 h-4 ml-2 transition-transform duration-200" id="dropdownArrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        
                        <!-- Dropdown Menu -->
                        <div id="userDropdownMenu" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 hidden z-50">
                            <div class="py-1">
                                <a href="{{ route('admin.profile') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    Profil Saya
                                </a>
                                <a href="{{ route('admin.settings') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    Pengaturan
                                </a>
                                <hr class="my-1">
                                <form action="{{ route('logout') }}" method="POST" class="block" id="logoutForm">
                                    @csrf
                                    <button type="submit" id="logoutBtn" class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors duration-200 relative">
                                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                        </svg>
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Active Users Card -->
            <div class="bg-gradient-to-br from-white to-amber-50 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 border border-amber-200 hover:border-amber-300">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-gradient-to-br from-amber-400 to-yellow-500 rounded-lg flex items-center justify-center shadow-md">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <p class="text-sm font-medium text-amber-700">Active Users</p>
                        <div class="flex items-baseline">
                            <p class="text-2xl font-bold text-amber-900" data-active-users>{{ number_format($activeUsers ?? 0) }}</p>
                            @php $val = $activeUsersChangePercent; @endphp
                            <p class="ml-2 flex items-center text-sm font-semibold {{ is_null($val) ? 'text-gray-400' : ($val > 0 ? 'text-green-600' : ($val < 0 ? 'text-red-600' : 'text-gray-500')) }}" title="{{ isset($usingIntraDayBaseline)&&$usingIntraDayBaseline && !is_null($val) ? 'Perubahan sejak awal hari ini' : 'Perubahan dibanding kemarin' }}">
                                @if(!is_null($val))
                                    @if($val > 0)
                                        <svg class="self-center flex-shrink-0 h-5 w-5 {{ $val>0?'text-green-500':'' }}" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                                    @elseif($val < 0)
                                        <svg class="self-center flex-shrink-0 h-5 w-5 text-red-500 rotate-180" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                                    @else
                                        <svg class="self-center flex-shrink-0 h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path d="M4 10h12v2H4z" /></svg>
                                    @endif
                                    <span class="ml-1">{{ $val > 0 ? '+' : '' }}{{ $val }}%</span>
                                @else
                                    <span class="ml-1">—</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Courses Card -->
            <div class="bg-gradient-to-br from-white to-yellow-50 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 border border-yellow-200 hover:border-yellow-300">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-gradient-to-br from-yellow-400 to-amber-500 rounded-lg flex items-center justify-center shadow-md">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <p class="text-sm font-medium text-yellow-700">Total Courses</p>
                        <div class="flex items-baseline">
                            <p class="text-2xl font-bold text-yellow-900">{{ number_format($totalCourses ?? 0) }}</p>
                            @php $val = $totalCoursesChangePercent; @endphp
                            <p class="ml-2 flex items-center text-sm font-semibold {{ is_null($val) ? 'text-gray-400' : ($val > 0 ? 'text-green-600' : ($val < 0 ? 'text-red-600' : 'text-gray-500')) }}" title="{{ isset($usingIntraDayBaseline)&&$usingIntraDayBaseline && !is_null($val) ? 'Perubahan sejak awal hari ini' : 'Perubahan dibanding kemarin' }}">
                                @if(!is_null($val))
                                    @if($val > 0)
                                        <svg class="self-center flex-shrink-0 h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                                    @elseif($val < 0)
                                        <svg class="self-center flex-shrink-0 h-5 w-5 text-red-500 rotate-180" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                                    @else
                                        <svg class="self-center flex-shrink-0 h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path d="M4 10h12v2H4z" /></svg>
                                    @endif
                                    <span class="ml-1">{{ $val > 0 ? '+' : '' }}{{ $val }}%</span>
                                @else
                                    <span class="ml-1">—</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Events Card -->
            <div class="bg-gradient-to-br from-white to-orange-50 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 border border-orange-200 hover:border-orange-300">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-gradient-to-br from-orange-400 to-amber-500 rounded-lg flex items-center justify-center shadow-md">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <p class="text-sm font-medium text-orange-700">Total Events</p>
                        <div class="flex items-baseline">
                            <p class="text-2xl font-bold text-orange-900">{{ number_format($totalEvents ?? 0) }}</p>
                            @php $val = $totalEventsChangePercent; @endphp
                            <p class="ml-2 flex items-center text-sm font-semibold {{ is_null($val) ? 'text-gray-400' : ($val > 0 ? 'text-green-600' : ($val < 0 ? 'text-red-600' : 'text-gray-500')) }}" title="{{ isset($usingIntraDayBaseline)&&$usingIntraDayBaseline && !is_null($val) ? 'Perubahan sejak awal hari ini' : 'Perubahan dibanding kemarin' }}">
                                @if(!is_null($val))
                                    @if($val > 0)
                                        <svg class="self-center flex-shrink-0 h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                                    @elseif($val < 0)
                                        <svg class="self-center flex-shrink-0 h-5 w-5 text-red-500 rotate-180" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                                    @else
                                        <svg class="self-center flex-shrink-0 h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path d="M4 10h12v2H4z" /></svg>
                                    @endif
                                    <span class="ml-1">{{ $val > 0 ? '+' : '' }}{{ $val }}%</span>
                                @else
                                    <span class="ml-1">—</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Revenue Card -->
            <div class="bg-gradient-to-br from-white to-yellow-100 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 border border-yellow-300 hover:border-yellow-400">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-gradient-to-br from-yellow-500 to-amber-600 rounded-lg flex items-center justify-center shadow-md">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <p class="text-sm font-medium text-yellow-800">Total Revenue</p>
                        <div class="flex items-baseline">
                            <p class="text-2xl font-bold text-yellow-900">Rp {{ number_format($totalRevenue ?? 0, 0, ',', '.') }}</p>
                            @php $val = $totalRevenueChangePercent; @endphp
                            <p class="ml-2 flex items-center text-sm font-semibold {{ is_null($val) ? 'text-gray-400' : ($val > 0 ? 'text-green-600' : ($val < 0 ? 'text-red-600' : 'text-gray-500')) }}" title="{{ isset($usingIntraDayBaseline)&&$usingIntraDayBaseline && !is_null($val) ? 'Perubahan sejak awal hari ini' : 'Perubahan dibanding kemarin' }}">
                                @if(!is_null($val))
                                    @if($val > 0)
                                        <svg class="self-center flex-shrink-0 h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                                    @elseif($val < 0)
                                        <svg class="self-center flex-shrink-0 h-5 w-5 text-red-500 rotate-180" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                                    @else
                                        <svg class="self-center flex-shrink-0 h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path d="M4 10h12v2H4z" /></svg>
                                    @endif
                                    <span class="ml-1">{{ $val > 0 ? '+' : '' }}{{ $val }}%</span>
                                @else
                                    <span class="ml-1">—</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Legend for percentage baseline -->
        <div class="flex justify-end mb-6">
            @if(isset($usingIntraDayBaseline) && $usingIntraDayBaseline)
                <span class="text-xs px-2 py-1 rounded bg-amber-100 text-amber-700 tracking-wide">Persentase dibanding awal hari ini</span>
            @else
                <span class="text-xs px-2 py-1 rounded bg-gray-100 text-gray-600 tracking-wide">Persentase dibanding kemarin</span>
            @endif
        </div>

        <!-- Quick Actions & Content Management -->
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-8 items-start">
            <!-- Quick Actions -->
            <div class="bg-gradient-to-br from-white to-amber-50 rounded-xl shadow-lg border border-amber-200 p-6 xl:col-span-2">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-amber-900">Quick Actions</h2>
                    <div class="w-10 h-10 bg-gradient-to-br from-amber-400 to-yellow-500 rounded-lg flex items-center justify-center shadow-md">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-4 gap-4 auto-rows-fr">
                    <!-- Add New Course -->
                    <button type="button" aria-label="Add New Course" class="group text-left focus:outline-none focus:ring-2 focus:ring-blue-400 rounded-lg" onclick="window.location.href='{{ route('admin.courses.create') }}'">
                        <div class="flex h-full items-center p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg border border-blue-100 hover:border-blue-200 transition-all duration-200 hover:shadow-md">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <h3 class="text-sm font-medium text-gray-900 group-hover:text-blue-600 transition-colors">Add New Course</h3>
                                <p class="text-xs text-gray-500 mt-1">Create and publish a new course</p>
                            </div>
                            <div class="flex-shrink-0">
                                <svg class="w-5 h-5 text-gray-400 group-hover:text-blue-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </div>
                        </div>
                    </button>

                    <!-- Add New Event -->
                    <button type="button" aria-label="Add New Event" class="group text-left focus:outline-none focus:ring-2 focus:ring-purple-400 rounded-lg" onclick="window.location.href='{{ route('admin.events.create') }}'">
                        <div class="flex h-full items-center p-4 bg-gradient-to-r from-purple-50 to-pink-50 rounded-lg border border-purple-100 hover:border-purple-200 transition-all duration-200 hover:shadow-md">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-purple-600 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <h3 class="text-sm font-medium text-gray-900 group-hover:text-purple-600 transition-colors">Add New Event</h3>
                                <p class="text-xs text-gray-500 mt-1">Schedule and create a new event</p>
                            </div>
                            <div class="flex-shrink-0">
                                <svg class="w-5 h-5 text-gray-400 group-hover:text-purple-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </div>
                        </div>
                    </button>

                    <!-- Manage Courses -->
                    <button type="button" aria-label="Manage Courses" class="group text-left focus:outline-none focus:ring-2 focus:ring-orange-400 rounded-lg" onclick="window.location.href='{{ route('admin.courses.index') }}'">
                        <div class="flex h-full items-center p-4 bg-gradient-to-r from-orange-50 to-yellow-50 rounded-lg border border-orange-100 hover:border-orange-200 transition-all duration-200 hover:shadow-md">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-orange-600 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <h3 class="text-sm font-medium text-gray-900 group-hover:text-orange-600 transition-colors">Manage Courses</h3>
                                <p class="text-xs text-gray-500 mt-1">View and manage all courses and modules</p>
                            </div>
                            <div class="flex-shrink-0">
                                <svg class="w-5 h-5 text-gray-400 group-hover:text-orange-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </div>
                        </div>
                    </button>

                    <!-- Manage Events (tambahan baru) -->
                    <button type="button" aria-label="Manage Events" class="group text-left focus:outline-none focus:ring-2 focus:ring-purple-400 rounded-lg" onclick="window.location.href='{{ route('admin.events.index') }}'">
                        <div class="flex h-full items-center p-4 bg-gradient-to-r from-purple-50 to-blue-50 rounded-lg border border-purple-100 hover:border-purple-200 transition-all duration-200 hover:shadow-md">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-purple-600 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <h3 class="text-sm font-medium text-gray-900 group-hover:text-purple-600 transition-colors">Manage Events</h3>
                                <p class="text-xs text-gray-500 mt-1">View and manage all events</p>
                            </div>
                            <div class="flex-shrink-0">
                                <svg class="w-5 h-5 text-gray-400 group-hover:text-purple-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </div>
                        </div>
                    </button>

                    <!-- Manage Users -->
                    <button type="button" aria-label="Manage Users" class="group text-left focus:outline-none focus:ring-2 focus:ring-gray-400 rounded-lg" onclick="window.location.href='{{ route('admin.users.index') }}'">
                        <div class="flex h-full items-center p-4 bg-gradient-to-r from-slate-50 to-gray-100 rounded-lg border border-gray-200 hover:border-gray-300 transition-all duration-200 hover:shadow-md">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-gray-600 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 0 0-5-4M9 20H4v-2a4 4 0 0 1 5-4m8-6a4 4 0 1 1-8 0 4 4 0 0 1 8 0m-4 6c-3.314 0-6 2.239-6 5v1h12v-1c0-2.761-2.686-5-6-5" />
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <h3 class="text-sm font-medium text-gray-900 group-hover:text-gray-700 transition-colors">Manage Users</h3>
                                <p class="text-xs text-gray-500 mt-1">Kelola akun & role pengguna</p>
                            </div>
                            <div class="flex-shrink-0">
                                <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </div>
                        </div>
                    </button>

                    <!-- View Reports -->
                    <button type="button" aria-label="View Analytics" class="group text-left focus:outline-none focus:ring-2 focus:ring-green-400 rounded-lg" onclick="window.location.href='{{ route('admin.reports') }}'">
                        <div class="flex h-full items-center p-4 bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg border border-green-100 hover:border-green-200 transition-all duration-200 hover:shadow-md">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-green-600 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <h3 class="text-sm font-medium text-gray-900 group-hover:text-green-600 transition-colors">View Analytics</h3>
                                <p class="text-xs text-gray-500 mt-1">Check detailed reports and analytics</p>
                            </div>
                            <div class="flex-shrink-0">
                                <svg class="w-5 h-5 text-gray-400 group-hover:text-green-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </div>
                        </div>
                    </button>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-gradient-to-br from-white to-yellow-50 rounded-xl shadow-lg border border-yellow-200 p-6 xl:col-span-1 h-full flex flex-col">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-yellow-900">Recent Activity</h2>
                    <div class="w-10 h-10 bg-gradient-to-br from-yellow-400 to-amber-500 rounded-lg flex items-center justify-center shadow-md">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>

                <div class="flow-root">
                    <ul class="-mb-8">
                        @forelse($recentActivities ?? [] as $index => $activity)
                        <li>
                            <div class="relative {{ $index < count($recentActivities) - 1 ? 'pb-8' : '' }}">
                                @if($index < count($recentActivities) - 1)
                                <span class="absolute top-5 left-5 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                @endif
                                <div class="relative flex items-start space-x-3">
                                    <div class="relative">
                                        <img class="h-10 w-10 rounded-full bg-gray-400 flex items-center justify-center ring-8 ring-white" src="{{ $activity['avatar'] }}" alt="{{ $activity['user'] }}">
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div>
                                            <div class="text-sm">
                                                <span class="font-medium text-gray-900">{{ $activity['user'] }}</span>
                                            </div>
                                            <p class="mt-0.5 text-sm text-gray-500">{{ $activity['action'] }}</p>
                                            @if(!empty($activity['description']))
                                                <p class="mt-1 text-xs text-gray-400 leading-snug">{{ $activity['description'] }}</p>
                                            @endif
                                        </div>
                                        <div class="mt-2 text-sm text-gray-700">
                                            <p>{{ $activity['time'] }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        @empty
                        <li>
                            <div class="text-center py-8">
                                <p class="text-gray-500">No recent activity</p>
                            </div>
                        </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gradient-to-r from-amber-600 to-yellow-500">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Brand Section -->
                <div class="space-y-4">
                    <div class="flex items-center space-x-3">
                        <img src="{{ asset('images/logo idspora_nobg_dark 1.png') }}" alt="idSpora Logo" class="h-8 w-auto">
                        <span class="text-xl font-bold text-white">idSpora</span>
                    </div>
                    <p class="text-amber-100 text-sm leading-relaxed">
                        Learning Management System yang memudahkan proses pembelajaran dan pengembangan skill di era digital.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-amber-100 hover:text-white transition-colors duration-200">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/>
                            </svg>
                        </a>
                        <a href="#" class="text-amber-100 hover:text-white transition-colors duration-200">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M22.46 6c-.77.35-1.6.58-2.46.69.88-.53 1.56-1.37 1.88-2.38-.83.5-1.75.85-2.72 1.05C18.37 4.5 17.26 4 16 4c-2.35 0-4.27 1.92-4.27 4.29 0 .34.04.67.11.98C8.28 9.09 5.11 7.38 3 4.79c-.37.63-.58 1.37-.58 2.15 0 1.49.75 2.81 1.91 3.56-.71 0-1.37-.2-1.95-.5v.03c0 2.08 1.48 3.82 3.44 4.21a4.22 4.22 0 0 1-1.93.07 4.28 4.28 0 0 0 4 2.98 8.521 8.521 0 0 1-5.33 1.84c-.34 0-.68-.02-1.02-.06C3.44 20.29 5.7 21 8.12 21 16 21 20.33 14.46 20.33 8.79c0-.19 0-.37-.01-.56.84-.6 1.56-1.36 2.14-2.23z"/>
                            </svg>
                        </a>
                        <a href="#" class="text-amber-100 hover:text-white transition-colors duration-200">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-white">Quick Links</h3>
                    <ul class="space-y-2">
                        <li><a href="{{ route('admin.dashboard') }}" class="text-amber-100 hover:text-white transition-colors duration-200 text-sm">Dashboard</a></li>
                        <li><a href="{{ route('admin.courses.index') }}" class="text-amber-100 hover:text-white transition-colors duration-200 text-sm">Manage Courses</a></li>
                        <li><a href="{{ route('admin.events.index') }}" class="text-amber-100 hover:text-white transition-colors duration-200 text-sm">Manage Events</a></li>
                        <li><a href="{{ route('admin.reports') }}" class="text-amber-100 hover:text-white transition-colors duration-200 text-sm">Analytics</a></li>
                        <li><a href="{{ route('landing-page') }}" class="text-amber-100 hover:text-white transition-colors duration-200 text-sm">Public Site</a></li>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-white">Contact Info</h3>
                    <div class="space-y-3">
                        <div class="flex items-center space-x-3">
                            <svg class="w-4 h-4 text-amber-200 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            <span class="text-amber-100 text-sm">admin@idspora.com</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <svg class="w-4 h-4 text-amber-200 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                            <span class="text-amber-100 text-sm">+62 21 1234 5678</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <svg class="w-4 h-4 text-amber-200 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span class="text-amber-100 text-sm">Jakarta, Indonesia</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Bar -->
            <div class="border-t border-amber-400/30 mt-8 pt-6">
                <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
                    <div class="text-amber-100 text-sm">
                        © {{ date('Y') }} idSpora. All rights reserved.
                    </div>
                    <div class="flex items-center space-x-6 text-sm">
                        <a href="#" class="text-amber-100 hover:text-white transition-colors duration-200">Privacy Policy</a>
                        <a href="#" class="text-amber-100 hover:text-white transition-colors duration-200">Terms of Service</a>
                        <a href="#" class="text-amber-100 hover:text-white transition-colors duration-200">Help Center</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
</div>



<script>
// Consolidated scripts
document.addEventListener('DOMContentLoaded', function() {
    initUserDropdown();
    initActiveUsersPoll();
    animateCounters();
    showFlashMessages();
    initExportButton();
});

function initUserDropdown() {
    const userDropdownButton = document.getElementById('userDropdownButton');
    const userDropdownMenu = document.getElementById('userDropdownMenu');
    const dropdownArrow = document.getElementById('dropdownArrow');
    if (!userDropdownButton || !userDropdownMenu) return;
    userDropdownButton.addEventListener('click', function(e) {
        e.stopPropagation();
        userDropdownMenu.classList.toggle('hidden');
        dropdownArrow && dropdownArrow.classList.toggle('rotate-180');
    });
    document.addEventListener('click', function(e) {
        if (!userDropdownButton.contains(e.target) && !userDropdownMenu.contains(e.target)) {
            userDropdownMenu.classList.add('hidden');
            dropdownArrow && dropdownArrow.classList.remove('rotate-180');
        }
    });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            userDropdownMenu.classList.add('hidden');
            dropdownArrow && dropdownArrow.classList.remove('rotate-180');
        }
    });
}

function initActiveUsersPoll() {
    setInterval(function() {
        fetch('{{ route("admin.active-users-count") }}')
            .then(r => r.json())
            .then(data => { if (data.count) { document.querySelector('[data-active-users]').textContent = data.count.toLocaleString(); } })
            .catch(() => {});
    }, 30000);
}

function animateCounters() {
    const counters = document.querySelectorAll('[data-active-users], .stat-animate');
    counters.forEach(counter => {
        const original = counter.textContent;
        const numeric = parseInt(original.replace(/[^0-9]/g, '')) || 0;
        const isCurrency = original.includes('Rp');
        let current = 0;
        const steps = 45;
        const increment = numeric / steps;
        const timer = setInterval(() => {
            current += increment;
            if (current >= numeric) { current = numeric; clearInterval(timer); }
            counter.textContent = (isCurrency ? 'Rp ' : '') + Math.floor(current).toLocaleString('id-ID');
        }, 16);
    });
}

function showFlashMessages() {
    @if(session('success'))
        createToast('success', `{{ addslashes(session('success')) }}`);
    @endif
    @if($errors->any())
        createToast('error', 'Please check the form for errors');
    @endif
}

function initExportButton(){
    const btn = document.getElementById('exportDataBtn');
    if(!btn) return;
    btn.addEventListener('click', function(){
        const url = btn.getAttribute('data-export-url');
        if(!url) return;
        const originalHtml = btn.innerHTML;
        btn.disabled = true;
        btn.classList.add('opacity-60','cursor-not-allowed');
        btn.innerHTML = `<svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke-width="4"></circle><path class="opacity-75" stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M4 12a8 8 0 018-8" /></svg><span>Mengunduh...</span>`;
        // Use iframe to not disturb current page state
        const iframe = document.createElement('iframe');
        iframe.style.display='none';
        iframe.src = url;
        document.body.appendChild(iframe);
        // Revert after some seconds (download starts)
        setTimeout(()=>{
            btn.disabled = false;
            btn.classList.remove('opacity-60','cursor-not-allowed');
            btn.innerHTML = originalHtml;
            setTimeout(()=> iframe.remove(), 60000); // cleanup after a minute
        }, 3000);
    });
}

function createToast(type, message) {
    const div = document.createElement('div');
    const colors = type === 'success' ? 'bg-green-500' : 'bg-red-500';
    const icon = type === 'success' ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />' : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />';
    div.className = `${colors} fixed top-4 right-4 text-white px-6 py-3 rounded-lg shadow-lg z-50 transform translate-x-full opacity-0 transition-all duration-300 flex items-center`;
    div.innerHTML = `<svg class='w-5 h-5 mr-2' fill='none' stroke='currentColor' viewBox='0 0 24 24'>${icon}</svg><span>${message}</span>`;
    document.body.appendChild(div);
    requestAnimationFrame(() => div.classList.remove('translate-x-full','opacity-0'));
    setTimeout(() => { div.classList.add('translate-x-full','opacity-0'); setTimeout(()=>div.remove(),300); }, 3000);
}

// Show success message after form submission
@if(session('success'))
    setTimeout(function() {
        const successDiv = document.createElement('div');
        successDiv.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 transform translate-x-full opacity-0 transition-all duration-300';
        successDiv.innerHTML = `
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                {{ session('success') }}
            </div>
        `;
        document.body.appendChild(successDiv);
        
        // Animate in
        setTimeout(() => {
            successDiv.classList.remove('translate-x-full', 'opacity-0');
        }, 100);
        
        // Animate out after 3 seconds
        setTimeout(() => {
            successDiv.classList.add('translate-x-full', 'opacity-0');
            setTimeout(() => {
                document.body.removeChild(successDiv);
            }, 300);
        }, 3000);
    }, 500);
@endif

// Show error messages
@if($errors->any())
    setTimeout(function() {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 transform translate-x-full opacity-0 transition-all duration-300';
        errorDiv.innerHTML = `
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Please check the form for errors
            </div>
        `;
        document.body.appendChild(errorDiv);
        
        // Animate in
        setTimeout(() => {
            errorDiv.classList.remove('translate-x-full', 'opacity-0');
        }, 100);
        
        // Animate out after 4 seconds
        setTimeout(() => {
            errorDiv.classList.add('translate-x-full', 'opacity-0');
            setTimeout(() => {
                document.body.removeChild(errorDiv);
            }, 300);
        }, 4000);
    }, 500);
@endif


// Simple footer positioning
document.addEventListener('DOMContentLoaded', function() {
    // Ensure content wrapper has proper flex layout
    const contentWrapper = document.querySelector('.content-wrapper');
    if (contentWrapper) {
        contentWrapper.style.minHeight = '100vh';
        contentWrapper.style.display = 'flex';
        contentWrapper.style.flexDirection = 'column';
    }
    
    // Ensure main content takes available space
    const main = document.querySelector('main');
    if (main) {
        main.style.flex = '1';
    }
    
    // Ensure footer sticks to bottom
    const footer = document.querySelector('footer');
    if (footer) {
        footer.style.marginTop = 'auto';
    }
});
</script>

<!-- Logout Success Modal -->
<div id="logoutSuccessModal" class="fixed inset-0 hidden items-center justify-center z-50">
    <div class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm" aria-hidden="true"></div>
    <div class="relative w-full max-w-xs bg-white rounded-2xl shadow-xl p-6 overflow-hidden animate-scaleIn">
        <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center shadow-lg relative">
            <svg class="w-12 h-12 text-white stroke-[3] animate-drawCheck" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
            </svg>
            <span class="absolute inset-0 rounded-full ring-4 ring-green-400/40 animate-pulseRing"></span>
        </div>
        <h3 class="text-lg font-semibold text-gray-800 text-center mb-1">Berhasil Logout</h3>
        <p class="text-sm text-gray-500 text-center mb-4">Sampai jumpa lagi! Anda akan dialihkan...</p>
        <div class="flex justify-center">
            <div class="h-1 w-40 bg-gray-200 rounded overflow-hidden">
                <div id="logoutProgress" class="h-full bg-green-500 w-0 animate-progressBar"></div>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes scaleIn {0%{transform:translateY(18px) scale(.9);opacity:0;}60%{transform:translateY(-4px) scale(1.02);}100%{transform:translateY(0) scale(1);opacity:1;}}
@keyframes drawCheck {0%{stroke-dasharray:48;stroke-dashoffset:48;opacity:0;}20%{opacity:1;}100%{stroke-dashoffset:0;opacity:1;}}
@keyframes pulseRing {0%{transform:scale(.6);opacity:.6;}70%{transform:scale(1);opacity:0;}100%{opacity:0;}}
@keyframes progressGrow {from{width:0;}to{width:100%;}}
.animate-scaleIn{animation:scaleIn .65s cubic-bezier(.16,.8,.24,1) forwards;}
.animate-drawCheck{animation:drawCheck .9s ease .25s forwards;}
.animate-pulseRing{animation:pulseRing 2.2s ease-out infinite;}
.animate-progressBar{animation:progressGrow 1.6s linear forwards;}
</style>

<script>
document.addEventListener('DOMContentLoaded', function(){
    const logoutBtn = document.getElementById('logoutBtn');
    const logoutForm = document.getElementById('logoutForm');
    const modal = document.getElementById('logoutSuccessModal');
    if(!logoutBtn || !logoutForm || !modal) return;
    let submitting = false;
    logoutBtn.addEventListener('click', function(e){
        // Prevent immediate submit; show modal first
        if(submitting) return; // avoid duplicate
        e.preventDefault();
        // Show modal
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        // After animation + short delay, submit form
        setTimeout(()=>{
            submitting = true;
            logoutForm.submit();
        }, 1100); // wait for check animation mostly done
    });
});
</script>

@endsection

<style>
/* Custom scrollbar styles */
::-webkit-scrollbar {
    width: 6px;
}

::-webkit-scrollbar-track {
    background: #f1f5f9;
}

::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Smooth transitions for all interactive elements */
.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 200ms;
}

/* Custom gradient backgrounds */
.bg-gradient-to-br {
    background-image: linear-gradient(to bottom right, var(--tw-gradient-stops));
}

/* Card hover effects */
.group:hover .group-hover\:shadow-lg {
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

/* Modal backdrop blur effect */
.backdrop-blur {
    backdrop-filter: blur(4px);
}

/* Loading spinner for form submissions */
.loading {
    position: relative;
    overflow: hidden;
}

.loading::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.8);
    display: flex;
    align-items: center;
    justify-content: center;
}

.loading::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 20px;
    height: 20px;
    border: 2px solid #e5e7eb;
    border-top: 2px solid #3b82f6;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    z-index: 1;
}

@keyframes spin {
    0% { transform: translate(-50%, -50%) rotate(0deg); }
    100% { transform: translate(-50%, -50%) rotate(360deg); }
}

/* Simple footer fix */
.content-wrapper {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
}

.content-wrapper main {
    flex: 1;
}

.content-wrapper footer {
    margin-top: auto;
}

/* User Dropdown Styles */
#userDropdownMenu {
    animation: dropdownFadeIn 0.2s ease-out;
}

#userDropdownMenu.hidden {
    animation: dropdownFadeOut 0.2s ease-in;
}

@keyframes dropdownFadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes dropdownFadeOut {
    from {
        opacity: 1;
        transform: translateY(0);
    }
    to {
        opacity: 0;
        transform: translateY(-10px);
    }
}

/* Dropdown arrow rotation */
#dropdownArrow {
    transition: transform 0.2s ease-in-out;
}

#dropdownArrow.rotate-180 {
    transform: rotate(180deg);
}

/* Dropdown menu hover effects */
#userDropdownMenu a:hover {
    background-color: #f3f4f6;
}

#userDropdownMenu button:hover {
    background-color: #fef2f2;
}
</style>