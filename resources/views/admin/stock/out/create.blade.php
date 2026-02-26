@extends('layouts.admin')

@section('title', 'خصم مخزون')
@section('page-title', 'خصم مخزون')

@section('content')

<div class="max-w-4xl mx-auto">
    
    <div class="mb-6">
        <a href="{{ route('admin.stock.out.index') }}" 
           class="text-blue-600 hover:text-blue-800 flex items-center gap-2">
            <i class="fas fa-arrow-right"></i>
            رجوع لقائمة الخصم
        </a>
    </div>
    
    <form method="POST" action="{{ route('admin.stock.out.store') }}" 
          x-data="stockOutForm()" 
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
                    <select name="warehouse_id" 
                            x-model="warehouseId"
                            @change="checkAvailableStock"
                            required 
                            class="w-full border border-gray-300 rounded-lg px-4 py-2">
                        <option value="">اختر المخزن</option>
                        @foreach($warehouses as $warehouse)
                        <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        نوع الحركة <span class="text-red-500">*</span>
                    </label>
                    <select name="movement_type" required class="w-full border border-gray-300 rounded-lg px-4 py-2">
                        <option value="out">خصم عادي</option>
                        <option value="damaged">تالف</option>
                        <option value="expired">منتهي الصلاحية</option>
                        <option value="returned">مرتجع</option>
                    </select>
                </div>
                
                <!-- Available Stock Display -->
                <div x-show="availableStock !== null" 
                     class="md:col-span-2 bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-blue-800">المخزون المتاح:</span>
                        <span class="text-xl font-bold" 
                              :class="availableStock > 0 ? 'text-blue-900' : 'text-red-600'"
                              x-text="availableStock + ' ' + unitName"></span>
                    </div>
                </div>
                
            </div>
        </div>
        
        <!-- Quantity -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">الكمية</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        الكمية المطلوب خصمها <span class="text-red-500">*</span>
                    </label>
                    <div class="flex gap-2">
                        <input type="number" 
                               name="quantity" 
                               x-model="quantity"
                               @input="validateQuantity"
                               required
                               step="0.01"
                               min="0.01"
                               class="flex-1 border rounded-lg px-4 py-2"
                               :class="quantityError ? 'border-red-500' : 'border-gray-300'">
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
                    <p x-show="quantityError" 
                       class="text-red-600 text-sm mt-2"
                       x-text="quantityError"></p>
                </div>
                
            </div>
        </div>
        
        <!-- Additional Info -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">معلومات إضافية</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        السبب
                    </label>
                    <input type="text" 
                           name="reason" 
                           class="w-full border border-gray-300 rounded-lg px-4 py-2"
                           placeholder="سبب الخصم">
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
            <a href="{{ route('admin.stock.out.index') }}" 
               class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                إلغاء
            </a>
            <button type="submit" 
                    :disabled="quantityError || !productId || !warehouseId || !quantity"
                    class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed">
                <i class="fas fa-check ml-2"></i>
                تأكيد الخصم
            </button>
        </div>
        
    </form>
    
</div>

@endsection

@push('scripts')
<script>
function stockOutForm() {
    return {
        productId: '',
        warehouseId: '',
        unitId: '',
        quantity: '',
        selectedProduct: null,
        availableStock: null,
        unitName: '',
        quantityError: '',
        
        productChanged() {
            const select = document.querySelector('select[name="product_id"]');
            const option = select.options[select.selectedIndex];
            
            if (option.value) {
                this.selectedProduct = {
                    base_unit_id: option.dataset.baseUnit,
                    unit_name: option.dataset.unitName
                };
                this.unitId = option.dataset.baseUnit;
                this.unitName = option.dataset.unitName;
                
                if (this.warehouseId) {
                    this.checkAvailableStock();
                }
            } else {
                this.selectedProduct = null;
                this.unitId = '';
                this.availableStock = null;
            }
        },
        
        async checkAvailableStock() {
            if (!this.productId || !this.warehouseId) return;
            
            try {
                const response = await fetch('{{ route("admin.stock.out.available-stock") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        product_id: this.productId,
                        warehouse_id: this.warehouseId
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.availableStock = data.available_stock;
                    this.unitName = data.unit;
                    this.validateQuantity();
                }
            } catch (error) {
                console.error('Error checking stock:', error);
            }
        },
        
        validateQuantity() {
            if (!this.quantity || !this.availableStock) {
                this.quantityError = '';
                return;
            }
            
            const qty = parseFloat(this.quantity);
            
            if (qty > this.availableStock) {
                this.quantityError = `الكمية المطلوبة أكبر من المتاح (${this.availableStock} ${this.unitName})`;
            } else if (qty <= 0) {
                this.quantityError = 'الكمية يجب أن تكون أكبر من صفر';
            } else {
                this.quantityError = '';
            }
        },
        
        submitForm(e) {
            if (this.quantityError) {
                alert(this.quantityError);
                return;
            }
            e.target.submit();
        }
    }
}
</script>
@endpush