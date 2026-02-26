@extends('layouts.admin')

@section('title', 'إضافة منتج جديد')
@section('page-title', 'إضافة منتج جديد')

@section('content')

<div class="max-w-4xl mx-auto">
    
    <div class="mb-6">
        <a href="{{ route('admin.products.index') }}" 
           class="text-blue-600 hover:text-blue-800 flex items-center gap-2">
            <i class="fas fa-arrow-right"></i>
            رجوع للمنتجات
        </a>
    </div>
    
    <form method="POST" action="{{ route('admin.products.store') }}" class="space-y-6">
        @csrf
        
        <!-- Basic Info -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">المعلومات الأساسية</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        اسم المنتج <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="name" 
                           value="{{ old('name') }}"
                           required
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        SKU <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="sku" 
                           value="{{ old('sku') }}"
                           required
                           class="w-full border border-gray-300 rounded-lg px-4 py-2">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Barcode
                    </label>
                    <input type="text" 
                           name="barcode" 
                           value="{{ old('barcode') }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        التصنيف <span class="text-red-500">*</span>
                    </label>
                    <select name="category_id" 
                            required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2">
                        <option value="">اختر التصنيف</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        العلامة التجارية
                    </label>
                    <select name="brand_id" class="w-full border border-gray-300 rounded-lg px-4 py-2">
                        <option value="">اختر العلامة التجارية</option>
                        @foreach($brands as $brand)
                        <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
                            {{ $brand->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        الوحدة الأساسية <span class="text-red-500">*</span>
                    </label>
                    <select name="base_unit_id" 
                            required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2">
                        <option value="">اختر الوحدة</option>
                        @foreach($units as $unit)
                        <option value="{{ $unit->id }}" {{ old('base_unit_id') == $unit->id ? 'selected' : '' }}>
                            {{ $unit->name }} ({{ $unit->short_name }})
                        </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        الوصف
                    </label>
                    <textarea name="description" 
                              rows="3"
                              class="w-full border border-gray-300 rounded-lg px-4 py-2">{{ old('description') }}</textarea>
                </div>
                
            </div>
        </div>
        
        <!-- Pricing -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">التسعير</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        سعر التكلفة
                    </label>
                    <input type="number" 
                           name="cost_price" 
                           value="{{ old('cost_price') }}"
                           step="0.01"
                           min="0"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        سعر البيع
                    </label>
                    <input type="number" 
                           name="selling_price" 
                           value="{{ old('selling_price') }}"
                           step="0.01"
                           min="0"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        نسبة الضريبة (%)
                    </label>
                    <input type="number" 
                           name="tax_percentage" 
                           value="{{ old('tax_percentage', 0) }}"
                           step="0.01"
                           min="0"
                           max="100"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2">
                </div>
                
            </div>
        </div>
        
        <!-- Stock Settings -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">إعدادات المخزون</h3>
            
            <div class="space-y-4">
                
                <div class="flex items-center gap-3">
                    <input type="checkbox" 
                           name="track_batches" 
                           id="track_batches"
                           value="1"
                           {{ old('track_batches', true) ? 'checked' : '' }}
                           class="w-4 h-4 text-blue-600 border-gray-300 rounded">
                    <label for="track_batches" class="text-sm font-medium text-gray-700">
                        تتبع بالدفعات (Batches)
                    </label>
                </div>
                
                <div class="flex items-center gap-3">
                    <input type="checkbox" 
                           name="has_expiry_date" 
                           id="has_expiry_date"
                           value="1"
                           {{ old('has_expiry_date') ? 'checked' : '' }}
                           class="w-4 h-4 text-blue-600 border-gray-300 rounded">
                    <label for="has_expiry_date" class="text-sm font-medium text-gray-700">
                        له تاريخ صلاحية
                    </label>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            استراتيجية السحب <span class="text-red-500">*</span>
                        </label>
                        <select name="withdrawal_strategy" 
                                required
                                class="w-full border border-gray-300 rounded-lg px-4 py-2">
                            <option value="fifo" {{ old('withdrawal_strategy', 'fifo') == 'fifo' ? 'selected' : '' }}>
                                FIFO (الأقدم أولاً)
                            </option>
                            <option value="fefo" {{ old('withdrawal_strategy') == 'fefo' ? 'selected' : '' }}>
                                FEFO (الأقرب للانتهاء أولاً)
                            </option>
                            <option value="manual" {{ old('withdrawal_strategy') == 'manual' ? 'selected' : '' }}>
                                يدوي
                            </option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            الحد الأدنى للتنبيه <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               name="alert_quantity" 
                               value="{{ old('alert_quantity', 10) }}"
                               required
                               min="0"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            التنبيه قبل انتهاء الصلاحية (يوم)
                        </label>
                        <input type="number" 
                               name="expiry_alert_days" 
                               value="{{ old('expiry_alert_days', 30) }}"
                               min="0"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2">
                    </div>
                    
                </div>
                
                <div class="flex items-center gap-3">
                    <input type="checkbox" 
                           name="auto_block_expired" 
                           id="auto_block_expired"
                           value="1"
                           {{ old('auto_block_expired', true) ? 'checked' : '' }}
                           class="w-4 h-4 text-blue-600 border-gray-300 rounded">
                    <label for="auto_block_expired" class="text-sm font-medium text-gray-700">
                        منع بيع المنتجات منتهية الصلاحية تلقائياً
                    </label>
                </div>
                
                <div class="flex items-center gap-3">
                    <input type="checkbox" 
                           name="is_active" 
                           id="is_active"
                           value="1"
                           {{ old('is_active', true) ? 'checked' : '' }}
                           class="w-4 h-4 text-blue-600 border-gray-300 rounded">
                    <label for="is_active" class="text-sm font-medium text-gray-700">
                        نشط
                    </label>
                </div>
                
            </div>
        </div>
        
        <!-- Actions -->
        <div class="flex items-center justify-end gap-4">
            <a href="{{ route('admin.products.index') }}" 
               class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                إلغاء
            </a>
            <button type="submit" 
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                حفظ المنتج
            </button>
        </div>
        
    </form>
    
</div>

@endsection