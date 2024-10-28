<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function index()
    {
        // Ordenamos las unidades por fecha de creación descendente
        $units = Unit::orderBy('created_at', 'desc')->get();
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

        // Establecemos active como true por defecto para nuevas unidades
        $validated['active'] = true;

        Unit::create($validated);

        return redirect()
            ->route('units.index')
            ->with('success', 'Unidad creada exitosamente.');
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

        // Aseguramos que active sea false si no viene en el request
        $validated['active'] = $request->has('active');

        $unit->update($validated);

        return redirect()
            ->route('units.index')
            ->with('success', 'Unidad actualizada exitosamente.');
    }

    public function destroy(Unit $unit)
    {
        try {
            $unit->delete();
            $message = ['success' => 'Unidad eliminada exitosamente.'];
        } catch (\Exception $e) {
            $message = ['error' => 'No se puede eliminar la unidad porque está en uso.'];
        }

        return redirect()
            ->route('units.index')
            ->with($message);
    }
}