@extends('layouts.admin')

@section('title', 'تفاصيل إضافة المخزون')
@section('page-title', 'تفاصيل إضافة المخزون')

@section('content')

<div class="max-w-4xl mx-auto">
    
    <div class="mb-6">
        <a href="{{ route('admin.stock.in.index') }}" 
           class="text-blue-600 hover:text-blue-800 flex items-center gap-2">
            <i class="fas fa-arrow-right"></i>
            رجوع لقائمة الإضافات
        </a>
    </div>
    
    <!-- Movement Header -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex items-start justify-between mb-6">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <h2 class="text-2xl font-bold text-gray-800">إضافة مخزون #{{ $movement->id }}</h2>
                    <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-semibold">
                        <i class="fas fa-arrow-down ml-1"></i>
                        إضافة
                    </span>
                </div>
                <p class="text-gray-500">{{ $movement->created_at->format('Y-m-d H:i') }}</p>
            </div>
            <button onclick="window.print()" 
                    class="bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg">
                <i class="fas fa-print ml-2"></i>
                طباعة
            </button>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div>
                <p class="text-sm text-gray-500 mb-1">المخزن</p>
                <p class="font-medium">{{ $movement->warehouse->name }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">بواسطة</p>
                <p class="font-medium">{{ $movement->creator->name ?? 'النظام' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">المرجع</p>
                <p class="font-medium">{{ $movement->reference_number ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">النوع</p>
                <p class="font-medium">{{ $movement->reference_type ?? 'يدوي' }}</p>
            </div>
        </div>
    </div>
    
    <!-- Product Info -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-lg font-semibold mb-4">معلومات المنتج</h3>
        
        <div class="flex items-center gap-4 mb-6">
            <div class="w-16 h-16 bg-gray-200 rounded flex items-center justify-center">
                <i class="fas fa-box text-2xl text-gray-400"></i>
            </div>
            <div>
                <h4 class="text-xl font-bold text-gray-800">{{ $movement->product->name }}</h4>
                <p class="text-gray-500">SKU: {{ $movement->product->sku }}</p>
            </div>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 pt-4 border-t">
            <div>
                <p class="text-sm text-gray-500 mb-1">الكمية</p>
                <p class="text-2xl font-bold text-green-600">
                    +{{ $movement->quantity }}
                </p>
                <p class="text-sm text-gray-500">{{ $movement->product->baseUnit->short_name }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">سعر الوحدة</p>
                <p class="text-xl font-bold">
                    {{ number_format($movement->average_unit_cost, 2) }}
                </p>
                <p class="text-sm text-gray-500">جنيه</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">إجمالي التكلفة</p>
                <p class="text-xl font-bold text-gray-800">
                    {{ number_format($movement->total_cost, 2) }}
                </p>
                <p class="text-sm text-gray-500">جنيه</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">الوحدة الأساسية</p>
                <p class="text-xl font-bold">
                    {{ $movement->product->baseUnit->name }}
                </p>
            </div>
        </div>
    </div>
    
    <!-- Batch Details -->
    @if($movement->batches->count() > 0)
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-lg font-semibold mb-4">تفاصيل الدفعات (Batches)</h3>
        
        @foreach($movement->batches as $movementBatch)
        @php
            $batch = $movementBatch->batch;
        @endphp
        <div class="border rounded-lg p-4 mb-4 last:mb-0">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h4 class="font-semibold text-gray-800">{{ $batch->batch_number }}</h4>
                    @if($batch->supplier_name)
                    <p class="text-sm text-gray-500">المورد: {{ $batch->supplier_name }}</p>
                    @endif
                </div>
                <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-sm font-medium">
                    {{ $batch->status }}
                </span>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <p class="text-xs text-gray-500">الكمية الداخلة</p>
                    <p class="font-medium">{{ $batch->quantity_in }} {{ $movement->product->baseUnit->short_name }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">الكمية المتبقية</p>
                    <p class="font-medium">{{ $batch->quantity_remaining }} {{ $movement->product->baseUnit->short_name }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">تاريخ الشراء</p>
                    <p class="font-medium">{{ $batch->purchase_date->format('Y-m-d') }}</p>
                </div>
                @if($batch->expiry_date)
                <div>
                    <p class="text-xs text-gray-500">تاريخ الصلاحية</p>
                    <p class="font-medium {{ $batch->isExpired() ? 'text-red-600' : ($batch->isExpiringSoon() ? 'text-orange-600' : 'text-gray-800') }}">
                        {{ $batch->expiry_date->format('Y-m-d') }}
                        @if(!$batch->isExpired())
                        <span class="text-xs">({{ $batch->daysUntilExpiry() }} يوم)</span>
                        @endif
                    </p>
                </div>
                @endif
            </div>
            
            @if($batch->notes)
            <div class="mt-4 pt-4 border-t">
                <p class="text-sm text-gray-600">{{ $batch->notes }}</p>
            </div>
            @endif
        </div>
        @endforeach
    </div>
    @endif
    
    <!-- Notes -->
    @if($movement->notes)
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">ملاحظات</h3>
        <p class="text-gray-700">{{ $movement->notes }}</p>
    </div>
    @endif
    
</div>

@endsection