<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function index()
    {
        $units = Unit::all();
        return view('units.index', compact('units'));
    }

    public function create()
    {
        return view('units.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:units'
        ]);

        Unit::create($validated);

        return redirect()
            ->route('units.index')
            ->with('success', 'Unidad creada exitosamente.');
    }

    public function show(Unit $unit)
    {
        return view('units.show', compact('unit'));
    }

    public function edit(Unit $unit)
    {
        return view('units.edit', compact('unit'));
    }

    public function update(Request $request, Unit $unit)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:units,name,' . $unit->id,
            'active' => 'boolean'
        ]);

        $unit->update($validated);

        return redirect()
            ->route('units.index')
            ->with('success', 'Unidad actualizada exitosamente.');
    }

    public function destroy(Unit $unit)
    {
        $unit->delete();

        return redirect()
            ->route('units.index')
            ->with('success', 'Unidad eliminada exitosamente.');
    }
}