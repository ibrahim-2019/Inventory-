<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'نظام إدارة المخزون')</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <style>
        [x-cloak] { display: none !important; }
    </style>
    
    @stack('styles')
</head>
<body class="bg-gray-100">
    
    <!-- Sidebar -->
    <div class="flex h-screen">
        
        <!-- Sidebar -->
        <aside class="w-64 bg-gray-800 text-white flex-shrink-0">
            <div class="p-4">
                <h1 class="text-xl font-bold">نظام المخزون</h1>
            </div>
            
            <nav class="mt-4">
                <a href="{{ route('admin.dashboard') }}" 
                   class="flex items-center px-4 py-3 hover:bg-gray-700 {{ request()->routeIs('admin.dashboard') ? 'bg-gray-700' : '' }}">
                    <i class="fas fa-home ml-3"></i>
                    لوحة التحكم
                </a>
                
                <a href="{{ route('admin.products.index') }}" 
                   class="flex items-center px-4 py-3 hover:bg-gray-700 {{ request()->routeIs('admin.products.*') ? 'bg-gray-700' : '' }}">
                    <i class="fas fa-box ml-3"></i>
                    المنتجات
                </a>
                
                <div class="px-4 py-2 text-gray-400 text-sm font-semibold">المخزون</div>
                
                <a href="{{ route('admin.stock.in.index') }}" 
                   class="flex items-center px-4 py-3 hover:bg-gray-700 {{ request()->routeIs('admin.stock.in.*') ? 'bg-gray-700' : '' }}">
                    <i class="fas fa-arrow-down ml-3"></i>
                    إضافة مخزون
                </a>
                
                <a href="{{ route('admin.stock.out.index') }}" 
                   class="flex items-center px-4 py-3 hover:bg-gray-700 {{ request()->routeIs('admin.stock.out.*') ? 'bg-gray-700' : '' }}">
                    <i class="fas fa-arrow-up ml-3"></i>
                    خصم مخزون
                </a>
                
                <a href="{{ route('admin.warehouses.index') }}" 
                   class="flex items-center px-4 py-3 hover:bg-gray-700 {{ request()->routeIs('admin.warehouses.*') ? 'bg-gray-700' : '' }}">
                    <i class="fas fa-warehouse ml-3"></i>
                    المخازن
                </a>
                
                <div class="px-4 py-2 text-gray-400 text-sm font-semibold">التقارير</div>
                
                <a href="{{ route('admin.reports.current-stock') }}" 
                   class="flex items-center px-4 py-3 hover:bg-gray-700">
                    <i class="fas fa-chart-bar ml-3"></i>
                    المخزون الحالي
                </a>
                
                <a href="{{ route('admin.reports.movements') }}" 
                   class="flex items-center px-4 py-3 hover:bg-gray-700">
                    <i class="fas fa-exchange-alt ml-3"></i>
                    حركات المخزون
                </a>
                
                <a href="{{ route('admin.reports.expiry') }}" 
                   class="flex items-center px-4 py-3 hover:bg-gray-700">
                    <i class="fas fa-calendar-times ml-3"></i>
                    تقرير الصلاحية
                </a>
                
                <div class="px-4 py-2 text-gray-400 text-sm font-semibold mt-4">الإعدادات</div>
                
                <a href="{{ route('admin.categories.index') }}" 
                   class="flex items-center px-4 py-3 hover:bg-gray-700">
                    <i class="fas fa-tags ml-3"></i>
                    التصنيفات
                </a>
                
                <a href="{{ route('admin.brands.index') }}" 
                   class="flex items-center px-4 py-3 hover:bg-gray-700">
                    <i class="fas fa-trademark ml-3"></i>
                    العلامات التجارية
                </a>
                
                <a href="{{ route('admin.units.index') }}" 
                   class="flex items-center px-4 py-3 hover:bg-gray-700">
                    <i class="fas fa-ruler ml-3"></i>
                    الوحدات
                </a>
            </nav>
        </aside>
        
        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            
            <!-- Top Bar -->
            <header class="bg-white shadow">
                <div class="flex items-center justify-between px-6 py-4">
                    <h2 class="text-xl font-semibold text-gray-800">
                        @yield('page-title', 'لوحة التحكم')
                    </h2>
                    
                    <div class="flex items-center gap-4">
                        <!-- Notifications -->
                        <button class="relative">
                            <i class="fas fa-bell text-gray-600 text-xl"></i>
                            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                                3
                            </span>
                        </button>
                        
                        <!-- User Menu -->
                        <div class="relative" x-data="{ open: false }" @keydown.escape="open = false">
                            <button @click="open = !open" class="flex items-center gap-2 focus:outline-none">
                                <span class="text-gray-700">{{ auth()->user()->name ?? 'Admin' }}</span>
                                <i class="fas fa-chevron-down text-gray-600"></i>
                            </button>

                            <div x-show="open" x-cloak @click.away="open = false" x-transition class="origin-top-right absolute mt-2 right-0 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                                <div class="py-1 text-right">
                                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">الملف الشخصي</a>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="w-full text-right px-4 py-2 text-sm text-red-600 hover:bg-gray-100">تسجيل الخروج</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            
            <!-- Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6">
                
                <!-- Alerts -->
                @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
                @endif
                
                @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
                @endif
                
                @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                
                @yield('content')
            </main>
        </div>
    </div>
    
    <!-- Alpine.js -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    
    @stack('scripts')
</body>
</html>