<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\StockMovement;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StockInController extends Controller
{
    protected $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    public function index(Request $request)
    {
        $query = StockMovement::with(['product.baseUnit', 'warehouse', 'creator'])
            ->where('movement_type', 'in');

        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->has('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        if ($request->has('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        $movements = $query->latest()->paginate(20);

        $warehouses = Warehouse::active()->get();
        $products = Product::active()->get();

        return Inertia::render('Admin/Stock/In/Index', [
            'movements' => $movements,
            'warehouses' => $warehouses,
            'products' => $products,
        ]);
    }

    public function create()
    {
        $products = Product::active()
            ->with(['baseUnit', 'unitConversions.unit'])
            ->get();
        $warehouses = Warehouse::active()->get();

        return Inertia::render('Admin/Stock/In/Create', [
            'products' => $products,
            'warehouses' => $warehouses,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'quantity' => 'required|numeric|min:0.01',
            'unit_id' => 'required|exists:units,id',
            'unit_cost' => 'required|numeric|min:0',
            'batch_number' => 'nullable|string|unique:product_stock_batches,batch_number',
            'supplier_name' => 'nullable|string',
            'purchase_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after:today',
            'manufacture_date' => 'nullable|date|before_or_equal:today',
            'reference_number' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        try {
            $result = $this->inventoryService->stockIn($validated);

            return redirect()
                ->route('admin.stock.in.show', $result['movement']->id)
                ->with('success', $result['message']);
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function show($id)
    {
        $movement = StockMovement::with([
            'product.baseUnit',
            'warehouse',
            'creator',
            'batches.batch'
        ])->findOrFail($id);

        return view('admin.stock.in.show', compact('movement'));
    }
}
