@extends('layouts.admin')

@section('title', 'التقارير')
@section('page-title', 'التقارير')

@section('content')

<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">التقارير والإحصائيات</h2>
    <p class="text-gray-600 mt-1">اختر التقرير المطلوب</p>
</div>

<!-- Reports Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    
    <!-- Current Stock Report -->
    <a href="{{ route('admin.reports.current-stock') }}" 
       class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow p-6 block">
        <div class="flex items-center gap-4 mb-4">
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-boxes text-blue-600 text-2xl"></i>
            </div>
            <div>
                <h3 class="font-bold text-gray-800">المخزون الحالي</h3>
                <p class="text-sm text-gray-500">عرض جميع المنتجات والكميات</p>
            </div>
        </div>
        <div class="text-left">
            <i class="fas fa-arrow-left text-blue-600"></i>
        </div>
    </a>
    
    <!-- Movements Report -->
    <a href="{{ route('admin.reports.movements') }}" 
       class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow p-6 block">
        <div class="flex items-center gap-4 mb-4">
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-exchange-alt text-green-600 text-2xl"></i>
            </div>
            <div>
                <h3 class="font-bold text-gray-800">حركات المخزون</h3>
                <p class="text-sm text-gray-500">تقرير الإضافات والخصم</p>
            </div>
        </div>
        <div class="text-left">
            <i class="fas fa-arrow-left text-green-600"></i>
        </div>
    </a>
    
    <!-- Expiry Report -->
    <a href="{{ route('admin.reports.expiry') }}" 
       class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow p-6 block">
        <div class="flex items-center gap-4 mb-4">
            <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-calendar-times text-red-600 text-2xl"></i>
            </div>
            <div>
                <h3 class="font-bold text-gray-800">تقرير الصلاحية</h3>
                <p class="text-sm text-gray-500">المنتجات منتهية أو قريبة من الانتهاء</p>
            </div>
        </div>
        <div class="text-left">
            <i class="fas fa-arrow-left text-red-600"></i>
        </div>
    </a>
    
    <!-- Stock Value Report -->
    <a href="{{ route('admin.reports.stock-value') }}" 
       class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow p-6 block">
        <div class="flex items-center gap-4 mb-4">
            <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-dollar-sign text-yellow-600 text-2xl"></i>
            </div>
            <div>
                <h3 class="font-bold text-gray-800">قيمة المخزون</h3>
                <p class="text-sm text-gray-500">تقرير القيمة المالية للمخزون</p>
            </div>
        </div>
        <div class="text-left">
            <i class="fas fa-arrow-left text-yellow-600"></i>
        </div>
    </a>
    
    <!-- Slow Moving Report -->
    <a href="{{ route('admin.reports.slow-moving') }}" 
       class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow p-6 block">
        <div class="flex items-center gap-4 mb-4">
            <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-snail text-orange-600 text-2xl"></i>
            </div>
            <div>
                <h3 class="font-bold text-gray-800">المخزون البطيء</h3>
                <p class="text-sm text-gray-500">المنتجات قليلة الحركة</p>
            </div>
        </div>
        <div class="text-left">
            <i class="fas fa-arrow-left text-orange-600"></i>
        </div>
    </a>
    
    <!-- Top Moving Report -->
    <a href="{{ route('admin.reports.top-moving') }}" 
       class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow p-6 block">
        <div class="flex items-center gap-4 mb-4">
            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-fire text-purple-600 text-2xl"></i>
            </div>
            <div>
                <h3 class="font-bold text-gray-800">الأكثر مبيعاً</h3>
                <p class="text-sm text-gray-500">المنتجات الأكثر حركة</p>
            </div>
        </div>
        <div class="text-left">
            <i class="fas fa-arrow-left text-purple-600"></i>
        </div>
    </a>
    
</div>

@endsection