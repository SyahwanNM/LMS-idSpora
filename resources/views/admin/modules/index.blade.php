@extends('layouts.app')

@section('title', 'Course Modules - ' . $course->name)

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center">
                    <a href="{{ route('admin.dashboard') }}" class="mr-4">
                        <svg class="w-6 h-6 text-gray-600 hover:text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Course Modules</h1>
                        <p class="text-sm text-gray-600">{{ $course->name }}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.courses.modules.create', $course) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        <span>Add Module</span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if(session('success'))
            <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if($modules->count() > 0)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Modules ({{ $modules->count() }})</h2>
                </div>
                
                <div class="divide-y divide-gray-200" id="modules-list">
                    @foreach($modules as $module)
                    <div class="p-6 hover:bg-gray-50 transition-colors duration-200" data-module-id="{{ $module->id }}">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-2">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                            <span class="text-sm font-semibold text-blue-600">{{ $module->order_no }}</span>
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="text-lg font-medium text-gray-900">{{ $module->title }}</h3>
                                        <div class="flex items-center space-x-4 mt-1">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                @if($module->type === 'video') bg-blue-100 text-blue-800
                                                @elseif($module->type === 'pdf') bg-red-100 text-red-800
                                                @else bg-green-100 text-green-800 @endif">
                                                {{ ucfirst($module->type) }}
                                            </span>
                                            <span class="text-sm text-gray-500">{{ $module->formatted_duration }}</span>
                                            @if($module->is_free)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    Free
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                
                                @if($module->description)
                                    <p class="text-gray-600 mt-2">{{ Str::limit($module->description, 150) }}</p>
                                @endif
                            </div>
                            
                            <div class="flex items-center space-x-2 ml-4">
                                <a href="{{ route('admin.courses.modules.show', [$course, $module]) }}" class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                    View
                                </a>
                                <a href="{{ route('admin.courses.modules.edit', [$course, $module]) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                    Edit
                                </a>
                                <form action="{{ route('admin.courses.modules.destroy', [$course, $module]) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this module?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 text-sm font-medium">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No modules</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by creating a new module.</p>
                <div class="mt-6">
                    <a href="{{ route('admin.courses.modules.create', $course) }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Add Module
                    </a>
                </div>
            </div>
        @endif
    </main>
</div>

<script>
// Drag and drop reordering functionality
let draggedElement = null;

document.addEventListener('DOMContentLoaded', function() {
    const modulesList = document.getElementById('modules-list');
    
    if (modulesList) {
        // Make modules sortable
        modulesList.addEventListener('dragstart', function(e) {
            draggedElement = e.target.closest('[data-module-id]');
            e.target.style.opacity = '0.5';
        });
        
        modulesList.addEventListener('dragend', function(e) {
            e.target.style.opacity = '1';
            draggedElement = null;
        });
        
        modulesList.addEventListener('dragover', function(e) {
            e.preventDefault();
        });
        
        modulesList.addEventListener('drop', function(e) {
            e.preventDefault();
            if (draggedElement) {
                const afterElement = getDragAfterElement(modulesList, e.clientY);
                if (afterElement == null) {
                    modulesList.appendChild(draggedElement);
                } else {
                    modulesList.insertBefore(draggedElement, afterElement);
                }
                updateOrderNumbers();
            }
        });
    }
});

function getDragAfterElement(container, y) {
    const draggableElements = [...container.querySelectorAll('[data-module-id]:not(.dragging)')];
    
    return draggableElements.reduce((closest, child) => {
        const box = child.getBoundingClientRect();
        const offset = y - box.top - box.height / 2;
        
        if (offset < 0 && offset > closest.offset) {
            return { offset: offset, element: child };
        } else {
            return closest;
        }
    }, { offset: Number.NEGATIVE_INFINITY }).element;
}

function updateOrderNumbers() {
    const modules = document.querySelectorAll('[data-module-id]');
    const orderData = [];
    
    modules.forEach((module, index) => {
        const moduleId = module.getAttribute('data-module-id');
        const orderNumber = index + 1;
        
        // Update visual order number
        const orderElement = module.querySelector('.w-8.h-8.bg-blue-100 span');
        if (orderElement) {
            orderElement.textContent = orderNumber;
        }
        
        orderData.push({
            id: moduleId,
            order_no: orderNumber
        });
    });
    
    // Send update to server
    fetch('{{ route("admin.courses.modules.reorder", $course) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ modules: orderData })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Order updated successfully');
        }
    })
    .catch(error => {
        console.error('Error updating order:', error);
    });
}
</script>

<!-- Footer -->
<footer class="bg-gradient-to-r from-amber-600 to-yellow-500 mt-12">
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
@endsection
