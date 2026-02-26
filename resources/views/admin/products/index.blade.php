@extends('layouts.admin')

@section('title', 'المنتجات')
@section('page-title', 'المنتجات')

@section('content')

<div class="mb-6 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">إدارة المنتجات</h2>
        <p class="text-gray-600 mt-1">عرض وإدارة جميع المنتجات</p>
    </div>
    <a href="{{ route('admin.products.create') }}" 
       class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg flex items-center gap-2">
        <i class="fas fa-plus"></i>
        إضافة منتج جديد
    </a>
</div>

<!-- Filters -->
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">بحث</label>
            <input type="text" 
                   name="search" 
                   value="{{ request('search') }}"
                   placeholder="اسم المنتج، SKU، Barcode"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2">
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
            <label class="block text-sm font-medium text-gray-700 mb-2">العلامة التجارية</label>
            <select name="brand_id" class="w-full border border-gray-300 rounded-lg px-4 py-2">
                <option value="">الكل</option>
                @foreach($brands as $brand)
                <option value="{{ $brand->id }}" {{ request('brand_id') == $brand->id ? 'selected' : '' }}>
                    {{ $brand->name }}
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
            <a href="{{ route('admin.products.index') }}" 
               class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg">
                <i class="fas fa-redo"></i>
            </a>
        </div>
        
    </form>
</div>

<!-- Products Table -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">المنتج</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">SKU</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">التصنيف</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الوحدة</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">المخزون</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">السعر</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الحالة</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">إجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($products as $product)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            @if($product->primaryImage())
                            <img src="{{ asset('storage/' . $product->primaryImage()->image_path) }}" 
                                 alt="{{ $product->name }}"
                                 class="w-10 h-10 rounded object-cover">
                            @else
                            <div class="w-10 h-10 bg-gray-200 rounded flex items-center justify-center">
                                <i class="fas fa-box text-gray-400"></i>
                            </div>
                            @endif
                            <div>
                                <div class="font-medium text-gray-900">{{ $product->name }}</div>
                                @if($product->brand)
                                <div class="text-sm text-gray-500">{{ $product->brand->name }}</div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                        {{ $product->sku }}
                    </td>
                    <td class="px-6 py-4 text-sm">
                        {{ $product->category->name ?? '-' }}
                    </td>
                    <td class="px-6 py-4 text-sm">
                        {{ $product->baseUnit->short_name }}
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $totalStock = $product->stock->sum('available_quantity');
                            $isLow = $totalStock <= $product->alert_quantity;
                        @endphp
                        <span class="font-medium {{ $isLow ? 'text-red-600' : 'text-gray-900' }}">
                            {{ $totalStock }}
                        </span>
                        @if($isLow)
                        <i class="fas fa-exclamation-triangle text-red-600 mr-1"></i>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm">
                        {{ number_format($product->selling_price ?? 0, 2) }} ج
                    </td>
                    <td class="px-6 py-4">
                        @if($product->is_active)
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                            نشط
                        </span>
                        @else
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                            غير نشط
                        </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.products.show', $product) }}" 
                               class="text-blue-600 hover:text-blue-800"
                               title="عرض">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.products.edit', $product) }}" 
                               class="text-yellow-600 hover:text-yellow-800"
                               title="تعديل">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" 
                                  action="{{ route('admin.products.destroy', $product) }}"
                                  class="inline"
                                  onsubmit="return confirm('هل أنت متأكد من حذف هذا المنتج؟')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="text-red-600 hover:text-red-800"
                                        title="حذف">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                        <i class="fas fa-box-open text-4xl mb-4 text-gray-300"></i>
                        <p>لا توجد منتجات</p>
                        <a href="{{ route('admin.products.create') }}" 
                           class="text-blue-600 hover:underline mt-2 inline-block">
                            إضافة منتج جديد
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    @if($products->hasPages())
    <div class="px-6 py-4 border-t">
        {{ $products->links() }}
    </div>
    @endif
</div>

@endsection