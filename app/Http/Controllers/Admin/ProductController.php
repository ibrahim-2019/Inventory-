<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Unit;
use App\Services\QRCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ProductController extends Controller
{
    protected $qrCodeService;

    public function __construct(QRCodeService $qrCodeService)
    {
        $this->qrCodeService = $qrCodeService;
    }

    public function index(Request $request)
    {
        $query = Product::with(['category', 'brand', 'baseUnit', 'stock']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        $products = $query->paginate(20);

        $categories = Category::active()->get();
        $brands = Brand::active()->get();

        return Inertia::render('Admin/Products/Index', [
            'products' => $products,
            'categories' => $categories,
            'brands' => $brands,
        ]);
    }

    public function create()
    {
        $categories = Category::active()->get();
        $brands = Brand::active()->get();
        $units = Unit::active()->get();

        return Inertia::render('Admin/Products/Create', [
            'categories' => $categories,
            'brands' => $brands,
            'units' => $units,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:products,sku',
            'barcode' => 'nullable|string|unique:products,barcode',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'base_unit_id' => 'required|exists:units,id',
            'description' => 'nullable|string',
            'cost_price' => 'nullable|numeric|min:0',
            'selling_price' => 'nullable|numeric|min:0',
            'tax_percentage' => 'nullable|numeric|min:0|max:100',
            'track_batches' => 'boolean',
            'has_expiry_date' => 'boolean',
            'withdrawal_strategy' => 'required|in:fifo,fefo,manual',
            'alert_quantity' => 'required|integer|min:0',
            'expiry_alert_days' => 'nullable|integer|min:0',
            'auto_block_expired' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        $product = Product::create($validated);

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'تم إضافة المنتج بنجاح');
    }

    public function show(Product $product)
    {
        $product->load([
            'category',
            'brand',
            'baseUnit',
            'images',
            'unitConversions.unit',
            'stock.warehouse',
            'batches' => function ($q) {
                $q->where('status', 'active')->orderBy('purchase_date', 'asc');
            }
        ]);

        // Recent movements
        $recentMovements = $product->movements()
            ->with(['warehouse', 'creator'])
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.products.show', compact('product', 'recentMovements'));
    }

    public function edit(Product $product)
    {
        $categories = Category::active()->get();
        $brands = Brand::active()->get();
        $units = Unit::active()->get();

        return view('admin.products.edit', compact('product', 'categories', 'brands', 'units'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:products,sku,' . $product->id,
            'barcode' => 'nullable|string|unique:products,barcode,' . $product->id,
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'base_unit_id' => 'required|exists:units,id',
            'description' => 'nullable|string',
            'cost_price' => 'nullable|numeric|min:0',
            'selling_price' => 'nullable|numeric|min:0',
            'tax_percentage' => 'nullable|numeric|min:0|max:100',
            'track_batches' => 'boolean',
            'has_expiry_date' => 'boolean',
            'withdrawal_strategy' => 'required|in:fifo,fefo,manual',
            'alert_quantity' => 'required|integer|min:0',
            'expiry_alert_days' => 'nullable|integer|min:0',
            'auto_block_expired' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        $product->update($validated);

        return redirect()
            ->route('admin.products.show', $product)
            ->with('success', 'تم تحديث المنتج بنجاح');
    }

    public function destroy(Product $product)
    {
        // Check if product has stock
        $hasStock = $product->stock()->where('total_quantity', '>', 0)->exists();

        if ($hasStock) {
            return back()->with('error', 'لا يمكن حذف منتج له مخزون');
        }

        $product->delete();

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'تم حذف المنتج بنجاح');
    }

    /**
     * Generate QR Code
     */
    public function generateQRCode(Product $product)
    {
        $result = $this->qrCodeService->generateForProduct($product->id);

        return response()->json([
            'success' => true,
            'qr_code_url' => $result['url'],
        ]);
    }
}
