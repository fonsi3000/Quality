<?php
namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function index(Request $request)
    {
        $query = Unit::query();

        // Aplicar filtros de búsqueda
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'LIKE', "%{$search}%");
        }

        // Aplicar filtro de estado si existe
        if ($request->filled('status')) {
            $query->where('active', $request->status === 'active');
        }

        // Ordenamiento
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        // Paginación
        $units = $query->paginate(10);
        $units->appends($request->except('page'));

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