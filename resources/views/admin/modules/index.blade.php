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
@endsection
