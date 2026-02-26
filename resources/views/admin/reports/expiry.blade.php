@extends('layouts.admin')

@section('title', 'تقرير الصلاحية')
@section('page-title', 'تقرير الصلاحية')

@section('content')

<div class="mb-6">
    <a href="{{ route('admin.reports.index') }}" 
       class="text-blue-600 hover:text-blue-800 flex items-center gap-2">
        <i class="fas fa-arrow-right"></i>
        رجوع للتقارير
    </a>
</div>

<!-- Filters -->
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">المخزن</label>
            <select name="warehouse_id" class="w-full border border-gray-300 rounded-lg px-4 py-2">
                <option value="">الكل</option>
                @foreach($warehouses as $warehouse)
                <option value="{{ $warehouse->id }}" {{ request('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                    {{ $warehouse->name }}
                </option>
                @endforeach
            </select>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">الحالة</label>
            <select name="status" class="w-full border border-gray-300 rounded-lg px-4 py-2">
                <option value="">الكل</option>
                <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>منتهية الصلاحية</option>
                <option value="expiring_7_days" {{ request('status') == 'expiring_7_days' ? 'selected' : '' }}>ستنتهي خلال 7 أيام</option>
                <option value="expiring_30_days" {{ request('status') == 'expiring_30_days' ? 'selected' : '' }}>ستنتهي خلال 30 يوم</option>
            </select>
        </div>
        
        <div class="flex items-end gap-2">
            <button type="submit" 
                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                <i class="fas fa-search ml-2"></i>
                بحث
            </button>
            <button type="button"
                    onclick="window.print()"
                    class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg">
                <i class="fas fa-print"></i>
            </button>
        </div>
        
    </form>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 mb-1">إجمالي الدفعات</p>
                <p class="text-3xl font-bold text-gray-800">{{ $report['summary']['total_batches'] }}</p>
            </div>
            <div class="bg-gray-100 rounded-full p-3">
                <i class="fas fa-boxes text-gray-600 text-2xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 mb-1">منتهية</p>
                <p class="text-3xl font-bold text-red-600">{{ $report['summary']['expired_count'] }}</p>
            </div>
            <div class="bg-red-100 rounded-full p-3">
                <i class="fas fa-times-circle text-red-600 text-2xl"></i>
            </div>
        </div>
        <div class="mt-2 pt-2 border-t">
            <p class="text-sm text-gray-500">
                قيمة: {{ number_format($report['summary']['expired_value'], 2) }} ج
            </p>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 mb-1">خلال 7 أيام</p>
                <p class="text-3xl font-bold text-orange-600">{{ $report['summary']['expiring_7_days_count'] }}</p>
            </div>
            <div class="bg-orange-100 rounded-full p-3">
                <i class="fas fa-exclamation-triangle text-orange-600 text-2xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 mb-1">خلال 30 يوم</p>
                <p class="text-3xl font-bold text-yellow-600">{{ $report['summary']['expiring_30_days_count'] }}</p>
            </div>
            <div class="bg-yellow-100 rounded-full p-3">
                <i class="fas fa-clock text-yellow-600 text-2xl"></i>
            </div>
        </div>
    </div>
    
</div>

<!-- Tabs -->
<div class="bg-white rounded-lg shadow mb-6" x-data="{ tab: 'expired' }">
    <div class="border-b">
        <div class="flex">
            <button @click="tab = 'expired'" 
                    :class="tab === 'expired' ? 'border-b-2 border-red-500 text-red-600' : 'text-gray-500'"
                    class="px-6 py-3 font-medium">
                منتهية الصلاحية ({{ $report['expired']->count() }})
            </button>
            <button @click="tab = 'expiring_7'" 
                    :class="tab === 'expiring_7' ? 'border-b-2 border-orange-500 text-orange-600' : 'text-gray-500'"
                    class="px-6 py-3 font-medium">
                خلال 7 أيام ({{ $report['expiring_7_days']->count() }})
            </button>
            <button @click="tab = 'expiring_30'" 
                    :class="tab === 'expiring_30' ? 'border-b-2 border-yellow-500 text-yellow-600' : 'text-gray-500'"
                    class="px-6 py-3 font-medium">
                خلال 30 يوم ({{ $report['expiring_30_days']->count() }})
            </button>
        </div>
    </div>
    
    <!-- Expired Tab -->
    <div x-show="tab === 'expired'" class="p-6">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">المنتج</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Batch</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">المخزن</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">الكمية</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">تاريخ الصلاحية</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">القيمة</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($report['expired'] as $batch)
                    <tr class="hover:bg-red-50">
                        <td class="px-4 py-3">
                            <div class="font-medium">{{ $batch->product->name }}</div>
                            <div class="text-sm text-gray-500">{{ $batch->product->sku }}</div>
                        </td>
                        <td class="px-4 py-3 text-sm">{{ $batch->batch_number }}</td>
                        <td class="px-4 py-3 text-sm">{{ $batch->warehouse->name }}</td>
                        <td class="px-4 py-3 font-medium text-red-600">
                            {{ $batch->quantity_remaining }} {{ $batch->product->baseUnit->short_name }}
                        </td>
                        <td class="px-4 py-3 text-sm text-red-600 font-medium">
                            {{ $batch->expiry_date->format('Y-m-d') }}
                            <div class="text-xs">منتهي منذ {{ abs($batch->daysUntilExpiry()) }} يوم</div>
                        </td>
                        <td class="px-4 py-3 text-sm font-medium">
                            {{ number_format($batch->quantity_remaining * $batch->unit_cost, 2) }} ج
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                            لا توجد منتجات منتهية الصلاحية
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Expiring 7 Days Tab -->
    <div x-show="tab === 'expiring_7'" class="p-6">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">المنتج</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Batch</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">المخزن</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">الكمية</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">تاريخ الصلاحية</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">المتبقي</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($report['expiring_7_days'] as $batch)
                    <tr class="hover:bg-orange-50">
                        <td class="px-4 py-3">
                            <div class="font-medium">{{ $batch->product->name }}</div>
                            <div class="text-sm text-gray-500">{{ $batch->product->sku }}</div>
                        </td>
                        <td class="px-4 py-3 text-sm">{{ $batch->batch_number }}</td>
                        <td class="px-4 py-3 text-sm">{{ $batch->warehouse->name }}</td>
                        <td class="px-4 py-3 font-medium">
                            {{ $batch->quantity_remaining }} {{ $batch->product->baseUnit->short_name }}
                        </td>
                        <td class="px-4 py-3 text-sm text-orange-600 font-medium">
                            {{ $batch->expiry_date->format('Y-m-d') }}
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 bg-orange-100 text-orange-800 rounded text-sm font-semibold">
                                {{ $batch->daysUntilExpiry() }} يوم
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                            لا توجد منتجات ستنتهي خلال 7 أيام
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Expiring 30 Days Tab -->
    <div x-show="tab === 'expiring_30'" class="p-6">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">المنتج</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Batch</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">المخزن</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">الكمية</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">تاريخ الصلاحية</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">المتبقي</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($report['expiring_30_days'] as $batch)
                    <tr class="hover:bg-yellow-50">
                        <td class="px-4 py-3">
                            <div class="font-medium">{{ $batch->product->name }}</div>
                            <div class="text-sm text-gray-500">{{ $batch->product->sku }}</div>
                        </td>
                        <td class="px-4 py-3 text-sm">{{ $batch->batch_number }}</td>
                        <td class="px-4 py-3 text-sm">{{ $batch->warehouse->name }}</td>
                        <td class="px-4 py-3 font-medium">
                            {{ $batch->quantity_remaining }} {{ $batch->product->baseUnit->short_name }}
                        </td>
                        <td class="px-4 py-3 text-sm">
                            {{ $batch->expiry_date->format('Y-m-d') }}
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded text-sm font-semibold">
                                {{ $batch->daysUntilExpiry() }} يوم
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                            لا توجد منتجات ستنتهي خلال 30 يوم
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection