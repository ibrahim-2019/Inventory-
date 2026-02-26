@extends('layouts.admin')

@section('title', $product->name)
@section('page-title', $product->name)

@section('content')

<div class="mb-6 flex items-center justify-between">
    <a href="{{ route('admin.products.index') }}" 
       class="text-blue-600 hover:text-blue-800 flex items-center gap-2">
        <i class="fas fa-arrow-right"></i>
        رجوع للمنتجات
    </a>
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.products.edit', $product) }}" 
           class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg">
            <i class="fas fa-edit ml-2"></i>
            تعديل
        </a>
        <button onclick="generateQRCode({{ $product->id }})"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
            <i class="fas fa-qrcode ml-2"></i>
            QR Code
        </button>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <!-- Main Info -->
    <div class="lg:col-span-2 space-y-6">
        
        <!-- Product Details -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">{{ $product->name }}</h2>
                    <p class="text-gray-500 mt-1">SKU: {{ $product->sku }}</p>
                </div>
                @if($product->is_active)
                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-semibold">
                    نشط
                </span>
                @else
                <span class="px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-sm font-semibold">
                    غير نشط
                </span>
                @endif
            </div>
            
            <div class="grid grid-cols-2 gap-4 mt-6">
                <div>
                    <p class="text-sm text-gray-500">التصنيف</p>
                    <p class="font-medium">{{ $product->category->name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">العلامة التجارية</p>
                    <p class="font-medium">{{ $product->brand->name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">الوحدة الأساسية</p>
                    <p class="font-medium">{{ $product->baseUnit->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Barcode</p>
                    <p class="font-medium">{{ $product->barcode ?? '-' }}</p>
                </div>
            </div>
            
            @if($product->description)
            <div class="mt-6 pt-6 border-t">
                <p class="text-sm text-gray-500 mb-2">الوصف</p>
                <p class="text-gray-700">{{ $product->description }}</p>
            </div>
            @endif
        </div>
        
        <!-- Pricing -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">التسعير</h3>
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <p class="text-sm text-gray-500">سعر التكلفة</p>
                    <p class="text-xl font-bold text-gray-800">
                        {{ number_format($product->cost_price ?? 0, 2) }} ج
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">سعر البيع</p>
                    <p class="text-xl font-bold text-green-600">
                        {{ number_format($product->selling_price ?? 0, 2) }} ج
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">الضريبة</p>
                    <p class="text-xl font-bold text-gray-800">
                        {{ $product->tax_percentage }}%
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Stock by Warehouse -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">المخزون حسب المخازن</h3>
            <div class="space-y-3">
                @forelse($product->stock as $stock)
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div>
                        <p class="font-medium">{{ $stock->warehouse->name }}</p>
                        <p class="text-sm text-gray-500">
                            متاح: {{ $stock->available_quantity }} {{ $product->baseUnit->short_name }}
                        </p>
                    </div>
                    <div class="text-left">
                        <p class="text-2xl font-bold {{ $stock->available_quantity <= $product->alert_quantity ? 'text-red-600' : 'text-gray-800' }}">
                            {{ $stock->total_quantity }}
                        </p>
                        <p class="text-sm text-gray-500">
                            قيمة: {{ number_format($stock->total_cost, 2) }} ج
                        </p>
                    </div>
                </div>
                @empty
                <p class="text-center text-gray-500 py-4">لا يوجد مخزون</p>
                @endforelse
            </div>
        </div>
        
        <!-- Active Batches -->
        @if($product->batches->count() > 0)
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">الدفعات النشطة (Batches)</h3>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">Batch</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">المخزن</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">الكمية</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">التكلفة</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">الصلاحية</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($product->batches as $batch)
                        <tr>
                            <td class="px-4 py-3 text-sm">{{ $batch->batch_number }}</td>
                            <td class="px-4 py-3 text-sm">{{ $batch->warehouse->name }}</td>
                            <td class="px-4 py-3 text-sm font-medium">
                                {{ $batch->quantity_remaining }} {{ $product->baseUnit->short_name }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                {{ number_format($batch->unit_cost, 2) }} ج
                            </td>
                            <td class="px-4 py-3 text-sm">
                                @if($batch->expiry_date)
                                    @if($batch->isExpired())
                                    <span class="text-red-600 font-medium">
                                        منتهي
                                    </span>
                                    @elseif($batch->isExpiringSoon())
                                    <span class="text-orange-600 font-medium">
                                        {{ $batch->expiry_date->format('Y-m-d') }}
                                        ({{ $batch->daysUntilExpiry() }} يوم)
                                    </span>
                                    @else
                                    <span class="text-gray-600">
                                        {{ $batch->expiry_date->format('Y-m-d') }}
                                    </span>
                                    @endif
                                @else
                                <span class="text-gray-400">-</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
        
        <!-- Recent Movements -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">آخر الحركات</h3>
            <div class="space-y-3">
                @forelse($recentMovements as $movement)
                <div class="flex items-center justify-between p-4 border rounded-lg">
                    <div class="flex items-center gap-3">
                        @if($movement->movement_type === 'in')
                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-arrow-down text-green-600"></i>
                        </div>
                        @else
                        <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-arrow-up text-red-600"></i>
                        </div>
                        @endif
                        <div>
                            <p class="font-medium">
                                {{ $movement->movement_type === 'in' ? 'إضافة مخزون' : 'خصم مخزون' }}
                            </p>
                            <p class="text-sm text-gray-500">
                                {{ $movement->warehouse->name }} • 
                                {{ $movement->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                    <div class="text-left">
                        <p class="font-bold text-lg">
                            {{ $movement->quantity }} {{ $product->baseUnit->short_name }}
                        </p>
                        <p class="text-sm text-gray-500">
                            {{ $movement->creator->name ?? 'النظام' }}
                        </p>
                    </div>
                </div>
                @empty
                <p class="text-center text-gray-500 py-4">لا توجد حركات</p>
                @endforelse
            </div>
        </div>
        
    </div>
    
    <!-- Sidebar -->
    <div class="space-y-6">
        
        <!-- QR Code -->
        @if($product->qr_code)
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">QR Code</h3>
            <div class="flex justify-center">
                <img src="{{ asset('storage/' . $product->qr_code) }}" 
                     alt="QR Code"
                     class="w-48 h-48">
            </div>
            <button onclick="printQRCode()"
                    class="w-full mt-4 bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg">
                <i class="fas fa-print ml-2"></i>
                طباعة
            </button>
        </div>
        @endif
        
        <!-- Stock Settings -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">إعدادات المخزون</h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">تتبع بالدفعات</span>
                    <span class="text-sm font-medium">
                        {{ $product->track_batches ? 'نعم' : 'لا' }}
                    </span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">له تاريخ صلاحية</span>
                    <span class="text-sm font-medium">
                        {{ $product->has_expiry_date ? 'نعم' : 'لا' }}
                    </span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">استراتيجية السحب</span>
                    <span class="text-sm font-medium">
                        {{ strtoupper($product->withdrawal_strategy) }}
                    </span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">الحد الأدنى للتنبيه</span>
                    <span class="text-sm font-medium">
                        {{ $product->alert_quantity }} {{ $product->baseUnit->short_name }}
                    </span>
                </div>
                @if($product->expiry_alert_days)
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">تنبيه الصلاحية</span>
                    <span class="text-sm font-medium">
                        {{ $product->expiry_alert_days }} يوم
                    </span>
                </div>
                @endif
            </div>
        </div>
        
        <!-- Unit Conversions -->
        @if($product->unitConversions->count() > 0)
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">تحويل الوحدات</h3>
            <div class="space-y-2">
                @foreach($product->unitConversions as $conversion)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                    <span class="text-sm">{{ $conversion->unit->name }}</span>
                    <span class="text-sm font-medium">
                        = {{ $conversion->conversion_factor }} {{ $product->baseUnit->short_name }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>
        @endif
        
        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">إجراءات سريعة</h3>
            <div class="space-y-2">
                <a href="{{ route('admin.stock.in.create', ['product_id' => $product->id]) }}" 
                   class="block w-full bg-green-600 hover:bg-green-700 text-white text-center px-4 py-2 rounded-lg">
                    <i class="fas fa-plus ml-2"></i>
                    إضافة مخزون
                </a>
                <a href="{{ route('admin.stock.out.create', ['product_id' => $product->id]) }}" 
                   class="block w-full bg-red-600 hover:bg-red-700 text-white text-center px-4 py-2 rounded-lg">
                    <i class="fas fa-minus ml-2"></i>
                    خصم مخزون
                </a>
            </div>
        </div>
        
    </div>
    
</div>

@endsection

@push('scripts')
<script>
function generateQRCode(productId) {
    // AJAX call to generate QR code
    alert('QR Code generation - implementation needed');
}

function printQRCode() {
    window.print();
}
</script>
@endpush