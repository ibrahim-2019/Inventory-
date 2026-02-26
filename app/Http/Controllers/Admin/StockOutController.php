<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\StockMovement;
use App\Services\InventoryService;
use Illuminate\Http\Request;

class StockOutController extends Controller
{
    protected $inventoryService;
    
    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }
    
    public function index(Request $request)
    {
        $query = StockMovement::with(['product.baseUnit', 'warehouse', 'creator'])
            ->whereIn('movement_type', ['out', 'damaged', 'expired']);
        
        // Filters
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
        
        if ($request->has('movement_type')) {
            $query->where('movement_type', $request->movement_type);
        }
        
        $movements = $query->latest()->paginate(20);
        
        $warehouses = Warehouse::active()->get();
        $products = Product::active()->get();
        
        return view('admin.stock.out.index', compact('movements', 'warehouses', 'products'));
    }
    
    public function create()
    {
        $products = Product::active()->with(['baseUnit', 'unitConversions.unit', 'stock'])->get();
        $warehouses = Warehouse::active()->get();
        
        return view('admin.stock.out.create', compact('products', 'warehouses'));
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'quantity' => 'required|numeric|min:0.01',
            'unit_id' => 'required|exists:units,id',
            'movement_type' => 'required|in:out,damaged,expired,returned',
            'reason' => 'nullable|string',
            'reference_number' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);
        
        try {
            $result = $this->inventoryService->stockOut($validated);
            
            return redirect()
                ->route('admin.stock.out.show', $result['movement']->id)
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
        
        return view('admin.stock.out.show', compact('movement'));
    }
    
    /**
     * Get available stock for a product
     */
    public function getAvailableStock(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'warehouse_id' => 'required|exists:warehouses,id',
        ]);
        
        $availableStock = $this->inventoryService->getAvailableStock(
            $validated['product_id'],
            $validated['warehouse_id']
        );
        
        $product = Product::with('baseUnit')->findOrFail($validated['product_id']);
        
        return response()->json([
            'success' => true,
            'available_stock' => $availableStock,
            'unit' => $product->baseUnit->short_name,
        ]);
    }
}