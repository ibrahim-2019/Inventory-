<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use App\Models\User;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    public function index()
    {
        $warehouses = Warehouse::with('manager')->paginate(20);
        
        return view('admin.warehouses.index', compact('warehouses'));
    }
    
    public function create()
    {
        $managers = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['warehouse_manager', 'admin']);
        })->get();
        
        return view('admin.warehouses.create', compact('managers'));
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:warehouses,code',
            'location' => 'nullable|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'manager_id' => 'nullable|exists:users,id',
            'is_active' => 'boolean',
        ]);
        
        $warehouse = Warehouse::create($validated);
        
        return redirect()
            ->route('admin.warehouses.show', $warehouse)
            ->with('success', 'تم إضافة المخزن بنجاح');
    }
    
    public function show(Warehouse $warehouse)
    {
        $warehouse->load(['manager', 'stock.product']);
        
        // Statistics
        $stats = [
            'total_products' => $warehouse->stock()->count(),
            'total_value' => $warehouse->stock()->sum('total_cost'),
            'low_stock_count' => $warehouse->stock()
                ->whereHas('product', function($q) {
                    $q->whereRaw('product_stock.available_quantity <= products.alert_quantity');
                })
                ->count(),
        ];
        
        return view('admin.warehouses.show', compact('warehouse', 'stats'));
    }
    
    public function edit(Warehouse $warehouse)
    {
        $managers = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['warehouse_manager', 'admin']);
        })->get();
        
        return view('admin.warehouses.edit', compact('warehouse', 'managers'));
    }
    
    public function update(Request $request, Warehouse $warehouse)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:warehouses,code,' . $warehouse->id,
            'location' => 'nullable|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'manager_id' => 'nullable|exists:users,id',
            'is_active' => 'boolean',
        ]);
        
        $warehouse->update($validated);
        
        return redirect()
            ->route('admin.warehouses.show', $warehouse)
            ->with('success', 'تم تحديث المخزن بنجاح');
    }
    
    public function destroy(Warehouse $warehouse)
    {
        // Check if warehouse has stock
        if ($warehouse->stock()->where('total_quantity', '>', 0)->exists()) {
            return back()->with('error', 'لا يمكن حذف مخزن يحتوي على مخزون');
        }
        
        $warehouse->delete();
        
        return redirect()
            ->route('admin.warehouses.index')
            ->with('success', 'تم حذف المخزن بنجاح');
    }
}