<?php

namespace App\Http\Controllers;

use App\Models\Position;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    public function index(Request $request)
    {
        $query = Position::query();

        // Búsqueda por nombre
        if ($request->has('search') && !empty($request->search)) {
            $query->where('name', 'LIKE', "%{$request->search}%");
        }

        // Ordenamiento
        $sortColumn = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');

        // Lista de columnas permitidas para ordenar
        $allowedColumns = ['name', 'created_at'];

        if (in_array($sortColumn, $allowedColumns)) {
            $query->orderBy($sortColumn, $direction);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // Paginación
        $positions = $query->paginate(10)->appends($request->query());

        return view('positions.index', compact('positions'));
    }

    public function create()
    {
        return view('positions.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:positions',
        ]);

        // Aseguramos que active sea true por defecto
        $validated['active'] = $request->has('active');

        Position::create($validated);

        return redirect()
            ->route('positions.index')
            ->with('success', 'Cargo creado exitosamente.');
    }

    public function edit(Position $position)
    {
        return view('positions.edit', compact('position'));
    }

    public function update(Request $request, Position $position)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:positions,name,' . $position->id,
        ]);

        // Aseguramos que active sea false si no viene en el request
        $validated['active'] = $request->has('active');

        $position->update($validated);

        return redirect()
            ->route('positions.index')
            ->with('success', 'Cargo actualizado exitosamente.');
    }

    public function destroy(Position $position)
    {
        try {
            $position->delete();
            $message = ['success' => 'Cargo eliminado exitosamente.'];
        } catch (\Exception $e) {
            $message = ['error' => 'No se puede eliminar el cargo porque está en uso.'];
        }

        return redirect()
            ->route('positions.index')
            ->with($message);
    }
}