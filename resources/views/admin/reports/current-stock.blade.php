@extends('layouts.admin')

@section('title', 'تقرير المخزون الحالي')
@section('page-title', 'تقرير المخزون الحالي')

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
    <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        
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
            <label class="block text-sm font-medium text-gray-700 mb-2">التصنيف</label>
            <select name="category_id" class="w-full border border-gray-300 rounded-lg px-4 py-2">
                <option value="">الكل</option>
                @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
                @endforeach
            </select>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">الحالة</label>
            <select name="low_stock_only" class="w-full border border-gray-300 rounded-lg px-4 py-2">
                <option value="">الكل</option>
                <option value="1" {{ request('low_stock_only') ? 'selected' : '' }}>منخفض المخزون فقط</option>
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
            <button type="button"
                    onclick="exportToExcel()"
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                <i class="fas fa-file-excel"></i>
            </button>
        </div>
        
    </form>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 mb-1">إجمالي المنتجات</p>
                <p class="text-3xl font-bold text-gray-800">{{ $report['summary']['total_products'] }}</p>
            </div>
            <div class="bg-blue-100 rounded-full p-3">
                <i class="fas fa-boxes text-blue-600 text-2xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 mb-1">قيمة المخزون</p>
                <p class="text-3xl font-bold text-green-600">{{ number_format($report['summary']['total_value'], 2) }}</p>
                <p class="text-sm text-gray-500">جنيه</p>
            </div>
            <div class="bg-green-100 rounded-full p-3">
                <i class="fas fa-dollar-sign text-green-600 text-2xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 mb-1">منتجات منخفضة</p>
                <p class="text-3xl font-bold text-red-600">{{ $report['summary']['low_stock_items'] }}</p>
            </div>
            <div class="bg-red-100 rounded-full p-3">
                <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
            </div>
        </div>
    </div>
    
</div>

<!-- Stock Table -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="p-6 border-b">
        <h3 class="text-lg font-semibold">المخزون التفصيلي</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full" id="stockTable">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">المنتج</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">SKU</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">التصنيف</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">المخزن</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الكمية</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">متوسط التكلفة</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">القيمة</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الحالة</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($report['stocks'] as $stock)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="font-medium text-gray-900">{{ $stock->product->name }}</div>
                        @if($stock->product->brand)
                        <div class="text-sm text-gray-500">{{ $stock->product->brand->name }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                        {{ $stock->product->sku }}
                    </td>
                    <td class="px-6 py-4 text-sm">
                        {{ $stock->product->category->name ?? '-' }}
                    </td>
                    <td class="px-6 py-4 text-sm">
                        {{ $stock->warehouse->name }}
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $isLow = $stock->available_quantity <= $stock->product->alert_quantity;
                        @endphp
                        <div class="font-medium {{ $isLow ? 'text-red-600' : 'text-gray-900' }}">
                            {{ $stock->available_quantity }} {{ $stock->product->baseUnit->short_name }}
                        </div>
                        @if($stock->reserved_quantity > 0)
                        <div class="text-xs text-gray-500">
                            محجوز: {{ $stock->reserved_quantity }}
                        </div>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm">
                        {{ number_format($stock->average_cost, 2) }} ج
                    </td>
                    <td class="px-6 py-4 text-sm font-medium">
                        {{ number_format($stock->total_cost, 2) }} ج
                    </td>
                    <td class="px-6 py-4">
                        @if($isLow)
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                            <i class="fas fa-exclamation-triangle ml-1"></i>
                            منخفض
                        </span>
                        @else
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                            <i class="fas fa-check ml-1"></i>
                            جيد
                        </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                        لا توجد نتائج
                    </td>
                </tr>
                @endforelse
            </tbody>
            
            @if($report['stocks']->count() > 0)
            <tfoot class="bg-gray-50 font-medium">
                <tr>
                    <td colspan="4" class="px-6 py-4 text-left">الإجمالي</td>
                    <td class="px-6 py-4">{{ $report['stocks']->sum('total_quantity') }}</td>
                    <td class="px-6 py-4">-</td>
                    <td class="px-6 py-4">{{ number_format($report['stocks']->sum('total_cost'), 2) }} ج</td>
                    <td></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script>
function exportToExcel() {
    // Simple export to Excel
    const table = document.getElementById('stockTable');
    const wb = XLSX.utils.table_to_book(table, {sheet: "المخزون"});
    XLSX.writeFile(wb, 'current-stock-report.xlsx');
}
</script>
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
@endpush