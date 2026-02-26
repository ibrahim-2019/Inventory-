@extends('layouts.admin')

@section('title', 'لوحة التحكم')
@section('page-title', 'لوحة التحكم')

@section('content')

<!-- Statistics Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 lg:gap-6 mb-6">
    
    <!-- Total Products -->
    <div class="bg-white rounded-lg shadow p-3 sm:p-4 lg:p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-xs sm:text-sm">إجمالي المنتجات</p>
                <p class="text-2xl sm:text-3xl font-bold text-gray-800">{{ $summary['total_products'] }}</p>
            </div>
            <div class="bg-blue-100 rounded-full p-2 sm:p-3">
                <i class="fas fa-box text-blue-600 text-lg sm:text-2xl"></i>
            </div>
        </div>
    </div>
    
    <!-- Total Warehouses -->
    <div class="bg-white rounded-lg shadow p-3 sm:p-4 lg:p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-xs sm:text-sm">المخازن</p>
                <p class="text-2xl sm:text-3xl font-bold text-gray-800">{{ $summary['total_warehouses'] }}</p>
            </div>
            <div class="bg-green-100 rounded-full p-2 sm:p-3">
                <i class="fas fa-warehouse text-green-600 text-lg sm:text-2xl"></i>
            </div>
        </div>
    </div>
    
    <!-- Low Stock -->
    <div class="bg-white rounded-lg shadow p-3 sm:p-4 lg:p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-xs sm:text-sm">منتجات منخفضة</p>
                <p class="text-2xl sm:text-3xl font-bold text-red-600">{{ $summary['low_stock_count'] }}</p>
            </div>
            <div class="bg-red-100 rounded-full p-2 sm:p-3">
                <i class="fas fa-exclamation-triangle text-red-600 text-lg sm:text-2xl"></i>
            </div>
        </div>
        <a href="{{ route('admin.reports.current-stock', ['low_stock_only' => 1]) }}" 
           class="text-red-600 text-xs sm:text-sm mt-2 inline-block hover:underline">
            عرض التفاصيل →
        </a>
    </div>
    
    <!-- Expiring Soon -->
    <div class="bg-white rounded-lg shadow p-3 sm:p-4 lg:p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-xs sm:text-sm">قريبة من الانتهاء</p>
                <p class="text-2xl sm:text-3xl font-bold text-orange-600">{{ $summary['expiring_soon'] }}</p>
            </div>
            <div class="bg-orange-100 rounded-full p-2 sm:p-3">
                <i class="fas fa-calendar-times text-orange-600 text-lg sm:text-2xl"></i>
            </div>
        </div>
        <a href="{{ route('admin.reports.expiry') }}" 
           class="text-orange-600 text-xs sm:text-sm mt-2 inline-block hover:underline">
            عرض التفاصيل →
        </a>
    </div>
    
</div>

<!-- Today's Activity -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-3 sm:gap-4 lg:gap-6 mb-6">
    
    <!-- Today's Movements -->
    <div class="bg-white rounded-lg shadow p-3 sm:p-4 lg:p-6">
        <h3 class="text-base sm:text-lg font-semibold mb-4">حركات اليوم</h3>
        <div class="space-y-4">
            <div class="flex items-center justify-between text-sm sm:text-base">
                <div class="flex items-center gap-2 sm:gap-3 flex-1">
                    <div class="bg-green-100 rounded-full p-1.5 sm:p-2 flex-shrink-0">
                        <i class="fas fa-arrow-down text-green-600 text-sm sm:text-base"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="font-medium truncate">إضافة مخزون</p>
                        <p class="text-xs sm:text-sm text-gray-500 truncate">{{ $summary['stock_in_today'] }} وحدة</p>
                    </div>
                </div>
                <span class="text-xl sm:text-2xl font-bold text-green-600 flex-shrink-0 ml-2">{{ $summary['movements_today'] }}</span>
            </div>
            
            <div class="flex items-center justify-between text-sm sm:text-base">
                <div class="flex items-center gap-2 sm:gap-3 flex-1">
                    <div class="bg-red-100 rounded-full p-1.5 sm:p-2 flex-shrink-0">
                        <i class="fas fa-arrow-up text-red-600 text-sm sm:text-base"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="font-medium truncate">خصم مخزون</p>
                        <p class="text-xs sm:text-sm text-gray-500 truncate">{{ $summary['stock_out_today'] }} وحدة</p>
                    </div>
                </div>
                <span class="text-xl sm:text-2xl font-bold text-red-600 flex-shrink-0 ml-2">{{ $summary['movements_today'] }}</span>
            </div>
        </div>
    </div>
    
    <!-- Stock Value -->
    <div class="bg-white rounded-lg shadow p-3 sm:p-4 lg:p-6">
        <h3 class="text-base sm:text-lg font-semibold mb-4">قيمة المخزون</h3>
        <div class="text-center">
            <p class="text-3xl sm:text-4xl font-bold text-gray-800">
                {{ number_format($summary['total_stock_value'], 2) }}
            </p>
            <p class="text-xs sm:text-base text-gray-500 mt-2">جنيه مصري</p>
        </div>
    </div>
    
</div>

<!-- Recent Movements -->
<div class="bg-white rounded-lg shadow mb-6">
    <div class="p-3 sm:p-4 lg:p-6 border-b">
        <h3 class="text-base sm:text-lg font-semibold">آخر الحركات</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm sm:text-base">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-2 sm:px-4 lg:px-6 py-2 sm:py-3 text-right text-xs font-medium text-gray-500 uppercase">المنتج</th>
                    <th class="hidden sm:table-cell px-2 sm:px-4 lg:px-6 py-2 sm:py-3 text-right text-xs font-medium text-gray-500 uppercase">النوع</th>
                    <th class="px-2 sm:px-4 lg:px-6 py-2 sm:py-3 text-right text-xs font-medium text-gray-500 uppercase">الكمية</th>
                    <th class="hidden md:table-cell px-2 sm:px-4 lg:px-6 py-2 sm:py-3 text-right text-xs font-medium text-gray-500 uppercase">المخزن</th>
                    <th class="hidden lg:table-cell px-2 sm:px-4 lg:px-6 py-2 sm:py-3 text-right text-xs font-medium text-gray-500 uppercase">بواسطة</th>
                    <th class="px-2 sm:px-4 lg:px-6 py-2 sm:py-3 text-right text-xs font-medium text-gray-500 uppercase">التاريخ</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($recentMovements as $movement)
                <tr class="hover:bg-gray-50">
                    <td class="px-2 sm:px-4 lg:px-6 py-2 sm:py-4">
                        <div class="font-medium text-gray-900 truncate text-sm sm:text-base">{{ $movement->product->name }}</div>
                        <div class="text-xs text-gray-500 truncate">{{ $movement->product->sku }}</div>
                    </td>
                    <td class="hidden sm:table-cell px-2 sm:px-4 lg:px-6 py-2 sm:py-4">
                        @if($movement->movement_type === 'in')
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 whitespace-nowrap">
                            إضافة
                        </span>
                        @else
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800 whitespace-nowrap">
                            خصم
                        </span>
                        @endif
                    </td>
                    <td class="px-2 sm:px-4 lg:px-6 py-2 sm:py-4 text-sm sm:text-base">
                        <span class="whitespace-nowrap">{{ $movement->quantity }} {{ $movement->product->baseUnit->short_name }}</span>
                    </td>
                    <td class="hidden md:table-cell px-2 sm:px-4 lg:px-6 py-2 sm:py-4 text-sm">{{ $movement->warehouse->name }}</td>
                    <td class="hidden lg:table-cell px-2 sm:px-4 lg:px-6 py-2 sm:py-4 text-sm">{{ $movement->creator->name ?? 'النظام' }}</td>
                    <td class="px-2 sm:px-4 lg:px-6 py-2 sm:py-4 text-xs sm:text-sm text-gray-500 whitespace-nowrap">
                        {{ $movement->created_at->diffForHumans() }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-2 sm:px-4 lg:px-6 py-4 text-center text-gray-500">
                        لا توجد حركات
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Low Stock & Expiring Products -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-3 sm:gap-4 lg:gap-6">
    
    <!-- Low Stock Products -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-3 sm:p-4 lg:p-6 border-b">
            <h3 class="text-base sm:text-lg font-semibold text-red-600">منتجات منخفضة المخزون</h3>
        </div>
        <div class="p-3 sm:p-4 lg:p-6">
            @forelse($lowStockProducts as $stock)
            <div class="flex items-center justify-between py-2 sm:py-3 border-b last:border-b-0 text-sm sm:text-base">
                <div class="flex-1 min-w-0">
                    <p class="font-medium truncate">{{ $stock->product->name }}</p>
                    <p class="text-xs sm:text-sm text-gray-500 truncate">{{ $stock->warehouse->name }}</p>
                </div>
                <div class="text-left flex-shrink-0 ml-2">
                    <p class="font-bold text-red-600 whitespace-nowrap">
                        {{ $stock->available_quantity }} {{ $stock->product->baseUnit->short_name }}
                    </p>
                    <p class="text-xs text-gray-500">
                        الحد: {{ $stock->product->alert_quantity }}
                    </p>
                </div>
            </div>
            @empty
            <p class="text-center text-gray-500 py-4 text-sm">لا توجد منتجات منخفضة</p>
            @endforelse
        </div>
    </div>
    
    <!-- Expiring Products -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-3 sm:p-4 lg:p-6 border-b">
            <h3 class="text-base sm:text-lg font-semibold text-orange-600">منتجات قريبة من الانتهاء</h3>
        </div>
        <div class="p-3 sm:p-4 lg:p-6">
            @forelse($expiringBatches as $batch)
            <div class="flex items-center justify-between py-2 sm:py-3 border-b last:border-b-0 text-sm sm:text-base">
                <div class="flex-1 min-w-0">
                    <p class="font-medium truncate">{{ $batch->product->name }}</p>
                    <p class="text-xs sm:text-sm text-gray-500 truncate">Batch: {{ $batch->batch_number }}</p>
                </div>
                <div class="text-left flex-shrink-0 ml-2">
                    <p class="font-bold text-orange-600 whitespace-nowrap">
                        {{ $batch->daysUntilExpiry() }} يوم
                    </p>
                    <p class="text-xs text-gray-500">
                        {{ $batch->expiry_date->format('Y-m-d') }}
                    </p>
                </div>
            </div>
            @empty
            <p class="text-center text-gray-500 py-4 text-sm">لا توجد منتجات قريبة من الانتهاء</p>
            @endforelse
        </div>
    </div>
    
</div>

@endsection