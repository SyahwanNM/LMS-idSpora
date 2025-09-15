@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <h1 class="text-2xl font-bold text-gray-900">Admin Dashboard</h1>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <a href="{{ route('welcome') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                            <span>Beranda</span>
                        </a>
                    </div>
                    <div class="relative">
                        <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5-5-5h5v-12"></path>
                            </svg>
                            <span>Export Data</span>
                        </button>
                    </div>
                    <div class="flex items-center space-x-3">
                        <img class="h-8 w-8 rounded-full" src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name ?? 'Admin') }}&background=3b82f6&color=fff" alt="Admin">
                        <div class="flex flex-col">
                            <span class="text-sm font-medium text-gray-700">{{ Auth::user()->name ?? 'Admin' }}</span>
                            <span class="text-xs text-gray-500">Administrator</span>
                        </div>
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm transition-colors duration-200">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Active Users Card -->
            <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow duration-200 p-6 border border-gray-100">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <p class="text-sm font-medium text-gray-600">Active Users</p>
                        <div class="flex items-baseline">
                            <p class="text-2xl font-semibold text-gray-900" data-active-users>{{ number_format($activeUsers ?? 0) }}</p>
                            <p class="ml-2 flex items-baseline text-sm font-semibold text-green-600">
                                <svg class="self-center flex-shrink-0 h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="sr-only">Increased by</span>
                                12%
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Courses Card -->
            <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow duration-200 p-6 border border-gray-100">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <p class="text-sm font-medium text-gray-600">Total Courses</p>
                        <div class="flex items-baseline">
                            <p class="text-2xl font-semibold text-gray-900">{{ number_format($totalCourses ?? 0) }}</p>
                            <p class="ml-2 flex items-baseline text-sm font-semibold text-blue-600">
                                <svg class="self-center flex-shrink-0 h-5 w-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="sr-only">Increased by</span>
                                8%
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Events Card -->
            <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow duration-200 p-6 border border-gray-100">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <p class="text-sm font-medium text-gray-600">Total Events</p>
                        <div class="flex items-baseline">
                            <p class="text-2xl font-semibold text-gray-900">{{ number_format($totalEvents ?? 0) }}</p>
                            <p class="ml-2 flex items-baseline text-sm font-semibold text-purple-600">
                                <svg class="self-center flex-shrink-0 h-5 w-5 text-purple-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="sr-only">Increased by</span>
                                15%
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Revenue Card -->
            <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow duration-200 p-6 border border-gray-100">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <p class="text-sm font-medium text-gray-600">Total Revenue</p>
                        <div class="flex items-baseline">
                            <p class="text-2xl font-semibold text-gray-900">Rp {{ number_format($totalRevenue ?? 0, 0, ',', '.') }}</p>
                            <p class="ml-2 flex items-baseline text-sm font-semibold text-yellow-600">
                                <svg class="self-center flex-shrink-0 h-5 w-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="sr-only">Increased by</span>
                                22%
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions & Content Management -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Quick Actions -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-semibold text-gray-900">Quick Actions</h2>
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                </div>

                <div class="space-y-4">
                    <!-- Add New Course -->
                    <div class="group cursor-pointer" onclick="openAddCourseModal()">
                        <div class="flex items-center p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg border border-blue-100 hover:border-blue-200 transition-all duration-200 hover:shadow-md">
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
                    </div>

                    <!-- Add New Event -->
                    <div class="group cursor-pointer" onclick="openAddEventModal()">
                        <div class="flex items-center p-4 bg-gradient-to-r from-purple-50 to-pink-50 rounded-lg border border-purple-100 hover:border-purple-200 transition-all duration-200 hover:shadow-md">
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
                    </div>

                    <!-- Manage Courses -->
                    <div class="group cursor-pointer" onclick="window.location.href='{{ route('admin.courses.index') }}'">
                        <div class="flex items-center p-4 bg-gradient-to-r from-orange-50 to-yellow-50 rounded-lg border border-orange-100 hover:border-orange-200 transition-all duration-200 hover:shadow-md">
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
                    </div>

                    <!-- Manage Events (tambahan baru) -->
                    <div class="group cursor-pointer" onclick="window.location.href='{{ route('admin.events.index') }}'">
                        <div class="flex items-center p-4 bg-gradient-to-r from-purple-50 to-blue-50 rounded-lg border border-purple-100 hover:border-purple-200 transition-all duration-200 hover:shadow-md">
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
                    </div>

                    <!-- View Reports -->
                    <div class="group cursor-pointer" onclick="window.location.href='{{ route('admin.reports') }}'">
                        <div class="flex items-center p-4 bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg border border-green-100 hover:border-green-200 transition-all duration-200 hover:shadow-md">
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
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-semibold text-gray-900">Recent Activity</h2>
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
</div>

<!-- Add Course Modal -->
<div id="addCourseModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeAddCourseModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="relative inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
            <form action="{{ route('admin.courses.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div>
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100">
                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-5">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Add New Course</h3>
                    </div>
                </div>
                <div class="mt-5 space-y-4">
                    <div>
                        <label for="course_name" class="block text-sm font-medium text-gray-700">Course Name</label>
                        <input type="text" name="name" id="course_name" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Enter course name">
                    </div>
                    <div>
                        <label for="course_category" class="block text-sm font-medium text-gray-700">Category</label>
                        <select name="category_id" id="course_category" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">Select Category</option>
                            @foreach(\App\Models\Category::all() as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="course_description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea name="description" id="course_description" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Enter course description"></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="course_level" class="block text-sm font-medium text-gray-700">Level</label>
                            <select name="level" id="course_level" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="beginner">Beginner</option>
                                <option value="intermediate">Intermediate</option>
                                <option value="advanced">Advanced</option>
                            </select>
                        </div>
                        <div>
                            <label for="course_duration" class="block text-sm font-medium text-gray-700">Duration (Hours)</label>
                            <input type="number" name="duration" id="course_duration" required min="0" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="0">
                        </div>
                    </div>
                    <div>
                        <label for="course_price" class="block text-sm font-medium text-gray-700">Price (Rp)</label>
                        <input type="number" name="price" id="course_price" required min="0" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="0">
                    </div>
                    <div>
                        <label for="course_image" class="block text-sm font-medium text-gray-700">Course Image</label>
                        <input type="file" name="image" id="course_image" accept="image/*" required class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>
                </div>
                <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:col-start-2 sm:text-sm">Create Course</button>
                    <button type="button" onclick="closeAddCourseModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:col-start-1 sm:text-sm">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Event Modal -->
<div id="addEventModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeAddEventModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="relative inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
            <form action="{{ route('admin.events.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div>
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-purple-100">
                        <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-5">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Add New Event</h3>
                    </div>
                </div>
                <div class="mt-5 space-y-4">
                    <div>
                        <label for="event_title" class="block text-sm font-medium text-gray-700">Event Title</label>
                        <input type="text" name="title" id="event_title" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm" placeholder="Enter event title">
                    </div>
                    <div>
                        <label for="event_speaker" class="block text-sm font-medium text-gray-700">Speaker</label>
                        <input type="text" name="speaker" id="event_speaker" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm" placeholder="Enter speaker name">
                    </div>
                    <div>
                        <label for="event_description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea name="description" id="event_description" rows="3" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm" placeholder="Enter event description"></textarea>
                    </div>
                    <div>
                        <label for="event_location" class="block text-sm font-medium text-gray-700">Location</label>
                        <input type="text" name="location" id="event_location" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm" placeholder="Enter event location">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="event_date" class="block text-sm font-medium text-gray-700">Event Date</label>
                            <input type="date" name="event_date" id="event_date" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                        </div>
                        <div>
                            <label for="event_time" class="block text-sm font-medium text-gray-700">Event Time</label>
                            <input type="time" name="event_time" id="event_time" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                        </div>
                    </div>
                    <div>
                        <label for="event_price" class="block text-sm font-medium text-gray-700">Ticket Price (Rp)</label>
                        <input type="number" name="price" id="event_price" required min="0" step="0.01" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm" placeholder="0">
                    </div>
                    <div>
                        <label for="event_image" class="block text-sm font-medium text-gray-700">Event Image</label>
                        <input type="file" name="image" id="event_image" accept="image/*" required class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100">
                        <img id="eventImagePreview" src="#" alt="Preview" style="display:none;max-width:100%;margin-top:10px;border-radius:8px;">
                    </div>
                </div>
                <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-purple-600 text-base font-medium text-white hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 sm:col-start-2 sm:text-sm">Create Event</button>
                    <button type="button" onclick="closeAddEventModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 sm:mt-0 sm:col-start-1 sm:text-sm">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Modal Functions
function openAddCourseModal() {
    document.getElementById('addCourseModal').classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

function closeAddCourseModal() {
    document.getElementById('addCourseModal').classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
}

function openAddEventModal() {
    document.getElementById('addEventModal').classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

function closeAddEventModal() {
    document.getElementById('addEventModal').classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
}

// Close modals when clicking outside
document.addEventListener('DOMContentLoaded', function() {
    // Close modal when pressing escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeAddCourseModal();
            closeAddEventModal();
        }
    });

    // Auto-refresh active users count every 30 seconds
    setInterval(function() {
        fetch('{{ route("admin.active-users-count") }}')
            .then(response => response.json())
            .then(data => {
                if (data.count) {
                    document.querySelector('[data-active-users]').textContent = data.count.toLocaleString();
                }
            })
            .catch(error => console.log('Error fetching active users:', error));
    }, 30000);

    // Animate counters on page load
    animateCounters();
});

// Counter Animation Function
function animateCounters() {
    const counters = document.querySelectorAll('.text-2xl');
    
    counters.forEach(counter => {
        const target = parseInt(counter.textContent.replace(/[^0-9]/g, ''));
        const increment = target / 100;
        let current = 0;
        
        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            
            if (counter.textContent.includes('Rp')) {
                counter.textContent = 'Rp ' + Math.floor(current).toLocaleString('id-ID');
            } else {
                counter.textContent = Math.floor(current).toLocaleString();
            }
        }, 20);
    });
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

// Preview gambar event pada modal tambah event
document.getElementById('event_image').addEventListener('change', function(event) {
    const [file] = event.target.files;
    const preview = document.getElementById('eventImagePreview');
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        }
        reader.readAsDataURL(file);
    } else {
        preview.src = '#';
        preview.style.display = 'none';
    }
});
</script>

@section('scripts')
<script>
    // Modal Functions
    function openAddCourseModal() {
        document.getElementById('addCourseModal').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeAddCourseModal() {
        document.getElementById('addCourseModal').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    function openAddEventModal() {
        document.getElementById('addEventModal').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeAddEventModal() {
        document.getElementById('addEventModal').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    // Close modals when clicking outside
    document.addEventListener('DOMContentLoaded', function() {
        // Close modal when pressing escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeAddCourseModal();
                closeAddEventModal();
            }
        });

        // Auto-refresh active users count every 30 seconds
        setInterval(function() {
            fetch('{{ route("admin.active-users-count") }}')
                .then(response => response.json())
                .then(data => {
                    if (data.count) {
                        document.querySelector('[data-active-users]').textContent = data.count.toLocaleString();
                    }
                })
                .catch(error => console.log('Error fetching active users:', error));
        }, 30000);

        // Animate counters on page load
        animateCounters();
    });

    // Counter Animation Function
    function animateCounters() {
        const counters = document.querySelectorAll('.text-2xl');
        
        counters.forEach(counter => {
            const target = parseInt(counter.textContent.replace(/[^0-9]/g, ''));
            const increment = target / 100;
            let current = 0;
            
            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                
                if (counter.textContent.includes('Rp')) {
                    counter.textContent = 'Rp ' + Math.floor(current).toLocaleString('id-ID');
                } else {
                    counter.textContent = Math.floor(current).toLocaleString();
                }
            }, 20);
        });
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

    // Preview gambar event pada modal tambah event
    document.getElementById('event_image').addEventListener('change', function(event) {
        const [file] = event.target.files;
        const preview = document.getElementById('eventImagePreview');
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
            reader.readAsDataURL(file);
        } else {
            preview.src = '#';
            preview.style.display = 'none';
        }
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
</style>