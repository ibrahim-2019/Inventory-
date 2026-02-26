<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function index()
    {
        $brands = Brand::paginate(20);
        return view('admin.brands.index', compact('brands'));
    }

    public function create()
    {
        return view('admin.brands.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $validated['slug'] = \Str::slug($validated['name']);
        $validated['is_active'] = true;

        Brand::create($validated);

        return redirect()->route('admin.brands.index')
            ->with('success', 'تم إضافة العلامة التجارية بنجاح');
    }

    public function edit(Brand $brand)
    {
        return view('admin.brands.edit', compact('brand'));
    }

    public function update(Request $request, Brand $brand)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $validated['slug'] = \Str::slug($validated['name']);

        $brand->update($validated);

        return redirect()->route('admin.brands.index')
            ->with('success', 'تم تحديث العلامة التجارية بنجاح');
    }

    public function destroy(Brand $brand)
    {
        $brand->delete();
        return redirect()->route('admin.brands.index')
            ->with('success', 'تم حذف العلامة التجارية بنجاح');
    }
}