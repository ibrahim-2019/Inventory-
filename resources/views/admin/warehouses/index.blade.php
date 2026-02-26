@extends('layouts.admin')

@section('title', 'المخازن')
@section('page-title', 'المخازن')

@section('content')

<div class="mb-6 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">إدارة المخازن</h2>
        <p class="text-gray-600 mt-1">عرض وإدارة جميع المخازن</p>
    </div>
    <a href="{{ route('admin.warehouses.create') }}" 
       class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg flex items-center gap-2">
        <i class="fas fa-plus"></i>
        إضافة مخزن جديد
    </a>
</div>

<!-- Warehouses Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    
    @forelse($warehouses as $warehouse)
    <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow">
        <div class="p-6">
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-warehouse text-blue-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-800">{{ $warehouse->name }}</h3>
                        <p class="text-sm text-gray-500">{{ $warehouse->code }}</p>
                    </div>
                </div>
                @if($warehouse->is_active)
                <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs font-semibold">
                    نشط
                </span>
                @else
                <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded text-xs font-semibold">
                    غير نشط
                </span>
                @endif
            </div>
            
            <div class="space-y-2 mb-4">
                @if($warehouse->location)
                <div class="flex items-center gap-2 text-sm text-gray-600">
                    <i class="fas fa-map-marker-alt text-gray-400"></i>
                    <span>{{ $warehouse->location }}</span>
                </div>
                @endif
                
                @if($warehouse->phone)
                <div class="flex items-center gap-2 text-sm text-gray-600">
                    <i class="fas fa-phone text-gray-400"></i>
                    <span>{{ $warehouse->phone }}</span>
                </div>
                @endif
                
                @if($warehouse->manager)
                <div class="flex items-center gap-2 text-sm text-gray-600">
                    <i class="fas fa-user text-gray-400"></i>
                    <span>{{ $warehouse->manager->name }}</span>
                </div>
                @endif
            </div>
            
            <div class="pt-4 border-t flex items-center justify-between">
                <a href="{{ route('admin.warehouses.show', $warehouse) }}" 
                   class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    عرض التفاصيل →
                </a>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.warehouses.edit', $warehouse) }}" 
                       class="text-yellow-600 hover:text-yellow-800"
                       title="تعديل">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form method="POST" 
                          action="{{ route('admin.warehouses.destroy', $warehouse) }}"
                          class="inline"
                          onsubmit="return confirm('هل أنت متأكد من حذف هذا المخزن؟')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="text-red-600 hover:text-red-800"
                                title="حذف">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-span-3 bg-white rounded-lg shadow p-12 text-center">
        <i class="fas fa-warehouse text-6xl text-gray-300 mb-4"></i>
        <p class="text-gray-500 mb-4">لا توجد مخازن</p>
        <a href="{{ route('admin.warehouses.create') }}" 
           class="text-blue-600 hover:underline">
            إضافة مخزن جديد
        </a>
    </div>
    @endforelse
    
</div>

<!-- Pagination -->
@if($warehouses->hasPages())
<div class="mt-6">
    {{ $warehouses->links() }}
</div>
@endif

@endsection