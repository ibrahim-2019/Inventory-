@extends('layouts.admin')

@section('title', 'سجل إضافة المخزون')
@section('page-title', 'سجل إضافة المخزون')

@section('content')

<div class="mb-6 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">سجل إضافة المخزون</h2>
        <p class="text-gray-600 mt-1">عرض جميع عمليات الإضافة</p>
    </div>
    <a href="{{ route('admin.stock.in.create') }}" 
       class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg flex items-center gap-2">
        <i class="fas fa-plus"></i>
        إضافة مخزون جديد
    </a>
</div>

<!-- Filters -->
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">من تاريخ</label>
            <input type="date" 
                   name="date_from" 
                   value="{{ request('date_from') }}"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">إلى تاريخ</label>
            <input type="date" 
                   name="date_to" 
                   value="{{ request('date_to') }}"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2">
        </div>
        
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
            <label class="block text-sm font-medium text-gray-700 mb-2">المنتج</label>
            <select name="product_id" class="w-full border border-gray-300 rounded-lg px-4 py-2">
                <option value="">الكل</option>
                @foreach($products as $product)
                <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                    {{ $product->name }}
                </option>
                @endforeach
            </select>
        </div>
        
        <div class="flex items-end gap-2">
            <button type="submit" 
                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                <i class="fas fa-search ml-2"></i>
                بحث
            </button>
            <a href="{{ route('admin.stock.in.index') }}" 
               class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg">
                <i class="fas fa-redo"></i>
            </a>
        </div>
        
    </form>
</div>

<!-- Movements Table -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">#</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">المنتج</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">المخزن</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الكمية</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">التكلفة</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">المرجع</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">بواسطة</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">التاريخ</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">إجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($movements as $movement)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm text-gray-500">
                        #{{ $movement->id }}
                    </td>
                    <td class="px-6 py-4">
                        <div class="font-medium text-gray-900">{{ $movement->product->name }}</div>
                        <div class="text-sm text-gray-500">{{ $movement->product->sku }}</div>
                    </td>
                    <td class="px-6 py-4 text-sm">
                        {{ $movement->warehouse->name }}
                    </td>
                    <td class="px-6 py-4">
                        <span class="font-medium text-green-600">
                            +{{ $movement->quantity }} {{ $movement->product->baseUnit->short_name }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <div class="font-medium">{{ number_format($movement->total_cost, 2) }} ج</div>
                        <div class="text-gray-500">{{ number_format($movement->average_unit_cost, 2) }} ج/وحدة</div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                        {{ $movement->reference_number ?? '-' }}
                    </td>
                    <td class="px-6 py-4 text-sm">
                        {{ $movement->creator->name ?? 'النظام' }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                        <div>{{ $movement->created_at->format('Y-m-d') }}</div>
                        <div class="text-xs">{{ $movement->created_at->format('H:i') }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <a href="{{ route('admin.stock.in.show', $movement->id) }}" 
                           class="text-blue-600 hover:text-blue-800"
                           title="عرض التفاصيل">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-6 py-12 text-center text-gray-500">
                        <i class="fas fa-inbox text-4xl mb-4 text-gray-300"></i>
                        <p>لا توجد عمليات إضافة</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
            
            <!-- Summary Footer -->
            @if($movements->count() > 0)
            <tfoot class="bg-gray-50 font-medium">
                <tr>
                    <td colspan="3" class="px-6 py-4 text-left">الإجمالي</td>
                    <td class="px-6 py-4 text-green-600">
                        +{{ number_format($movements->sum('quantity'), 2) }}
                    </td>
                    <td class="px-6 py-4">
                        {{ number_format($movements->sum('total_cost'), 2) }} ج
                    </td>
                    <td colspan="4"></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
    
    <!-- Pagination -->
    @if($movements->hasPages())
    <div class="px-6 py-4 border-t">
        {{ $movements->links() }}
    </div>
    @endif
</div>

@endsection