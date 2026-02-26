@extends('layouts.admin')

@section('title', 'إضافة مخزون')
@section('page-title', 'إضافة مخزون')

@section('content')

<div class="max-w-4xl mx-auto">
    
    <div class="mb-6">
        <a href="{{ route('admin.stock.in.index') }}" 
           class="text-blue-600 hover:text-blue-800 flex items-center gap-2">
            <i class="fas fa-arrow-right"></i>
            رجوع لقائمة الإضافات
        </a>
    </div>
    
    <form method="POST" action="{{ route('admin.stock.in.store') }}" 
          x-data="stockInForm()" 
          @submit.prevent="submitForm"
          class="space-y-6">
        @csrf
        
        <!-- Product Selection -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">معلومات المنتج</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        المنتج <span class="text-red-500">*</span>
                    </label>
                    <select name="product_id" 
                            x-model="productId"
                            @change="productChanged"
                            required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2">
                        <option value="">اختر المنتج</option>
                        @foreach($products as $product)
                        <option value="{{ $product->id }}" 
                                data-base-unit="{{ $product->base_unit_id }}"
                                data-unit-name="{{ $product->baseUnit->short_name }}">
                            {{ $product->name }} ({{ $product->sku }})
                        </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        المخزن <span class="text-red-500">*</span>
                    </label>
                    <select name="warehouse_id" required class="w-full border border-gray-300 rounded-lg px-4 py-2">
                        <option value="">اختر المخزن</option>
                        @foreach($warehouses as $warehouse)
                        <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        رقم المرجع
                    </label>
                    <input type="text" 
                           name="reference_number" 
                           class="w-full border border-gray-300 rounded-lg px-4 py-2"
                           placeholder="اختياري">
                </div>
                
            </div>
        </div>
        
        <!-- Quantity & Pricing -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">الكمية والتسعير</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        الكمية <span class="text-red-500">*</span>
                    </label>
                    <div class="flex gap-2">
                        <input type="number" 
                               name="quantity" 
                               x-model="quantity"
                               required
                               step="0.01"
                               min="0.01"
                               class="flex-1 border border-gray-300 rounded-lg px-4 py-2">
                        <select name="unit_id" 
                                x-model="unitId"
                                required
                                class="border border-gray-300 rounded-lg px-4 py-2">
                            <option value="">الوحدة</option>
                            <template x-if="selectedProduct">
                                <option :value="selectedProduct.base_unit_id" 
                                        x-text="selectedProduct.unit_name"></option>
                            </template>
                        </select>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        سعر الوحدة <span class="text-red-500">*</span>
                    </label>
                    <input type="number" 
                           name="unit_cost" 
                           x-model="unitCost"
                           required
                           step="0.01"
                           min="0"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2">
                </div>
                
                <div class="md:col-span-2 bg-gray-50 p-4 rounded-lg">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-700 font-medium">إجمالي التكلفة:</span>
                        <span class="text-2xl font-bold text-gray-900" x-text="totalCost"></span>
                    </div>
                </div>
                
            </div>
        </div>
        
        <!-- Batch Info -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">معلومات الدفعة (Batch)</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        رقم الدفعة (Batch)
                    </label>
                    <input type="text" 
                           name="batch_number" 
                           class="w-full border border-gray-300 rounded-lg px-4 py-2"
                           placeholder="سيتم التوليد تلقائياً">
                    <p class="text-xs text-gray-500 mt-1">اتركه فارغاً للتوليد التلقائي</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        اسم المورد
                    </label>
                    <input type="text" 
                           name="supplier_name" 
                           class="w-full border border-gray-300 rounded-lg px-4 py-2">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        تاريخ الشراء
                    </label>
                    <input type="date" 
                           name="purchase_date" 
                           value="{{ date('Y-m-d') }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        تاريخ الإنتاج
                    </label>
                    <input type="date" 
                           name="manufacture_date" 
                           class="w-full border border-gray-300 rounded-lg px-4 py-2">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        تاريخ الصلاحية
                    </label>
                    <input type="date" 
                           name="expiry_date" 
                           class="w-full border border-gray-300 rounded-lg px-4 py-2">
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        ملاحظات
                    </label>
                    <textarea name="notes" 
                              rows="3"
                              class="w-full border border-gray-300 rounded-lg px-4 py-2"></textarea>
                </div>
                
            </div>
        </div>
        
        <!-- Actions -->
        <div class="flex items-center justify-end gap-4">
            <a href="{{ route('admin.stock.in.index') }}" 
               class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                إلغاء
            </a>
            <button type="submit" 
                    class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg">
                <i class="fas fa-check ml-2"></i>
                حفظ الإضافة
            </button>
        </div>
        
    </form>
    
</div>

@endsection

@push('scripts')
<script>
function stockInForm() {
    return {
        productId: '',
        unitId: '',
        quantity: '',
        unitCost: '',
        selectedProduct: null,
        
        get totalCost() {
            if (this.quantity && this.unitCost) {
                return (parseFloat(this.quantity) * parseFloat(this.unitCost)).toFixed(2) + ' ج';
            }
            return '0.00 ج';
        },
        
        productChanged() {
            const select = document.querySelector('select[name="product_id"]');
            const option = select.options[select.selectedIndex];
            
            if (option.value) {
                this.selectedProduct = {
                    base_unit_id: option.dataset.baseUnit,
                    unit_name: option.dataset.unitName
                };
                this.unitId = option.dataset.baseUnit;
            } else {
                this.selectedProduct = null;
                this.unitId = '';
            }
        },
        
        submitForm(e) {
            e.target.submit();
        }
    }
}
</script>
@endpush