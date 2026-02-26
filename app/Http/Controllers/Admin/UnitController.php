<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function index()
    {
        $units = Unit::paginate(20);
        return view('admin.units.index', compact('units'));
    }

    public function create()
    {
        return view('admin.units.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'short_name' => 'required|string|max:10',
            'type' => 'required|in:countable,weight,volume',
        ]);

        $validated['is_active'] = true;

        Unit::create($validated);

        return redirect()->route('admin.units.index')
            ->with('success', 'تم إضافة الوحدة بنجاح');
    }

    public function edit(Unit $unit)
    {
        return view('admin.units.edit', compact('unit'));
    }

    public function update(Request $request, Unit $unit)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'short_name' => 'required|string|max:10',
            'type' => 'required|in:countable,weight,volume',
        ]);

        $unit->update($validated);

        return redirect()->route('admin.units.index')
            ->with('success', 'تم تحديث الوحدة بنجاح');
    }

    public function destroy(Unit $unit)
    {
        $unit->delete();
        return redirect()->route('admin.units.index')
            ->with('success', 'تم حذف الوحدة بنجاح');
    }
}