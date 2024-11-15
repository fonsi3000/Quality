<?php

namespace App\Http\Controllers;

use App\Models\Process;
use Illuminate\Http\Request;

class ProcessController extends Controller
{
    public function index(Request $request)
    {
        $query = Process::query();

        // Búsqueda
        if ($request->has('search') && $request->search !== null) {
            $searchTerm = $request->search;
            $query->where('name', 'LIKE', "%{$searchTerm}%");
        }

        // Ordenamiento
        $sortColumn = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');

        // Validamos que la columna de ordenamiento sea válida
        if (in_array($sortColumn, ['name', 'created_at'])) {
            $query->orderBy($sortColumn, $sortDirection);
        }

        // Paginación manteniendo los parámetros de query
        $processes = $query->paginate(10);
        $processes->appends($request->query());

        if ($request->ajax()) {
            return response()->json([
                'table' => view('processes._table_body', compact('processes'))->render(),
                'pagination' => view('pagination.tailwind', ['paginator' => $processes])->render()
            ]);
        }

        return view('processes.index', compact('processes'));
    }

    public function create()
    {
        return view('processes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:processes',
            'active' => 'boolean'
        ]);

        $validated['active'] = $request->has('active');

        Process::create($validated);

        return redirect()
            ->route('processes.index')
            ->with('success', 'Proceso creado exitosamente.');
    }

    public function edit(Process $process)
    {
        return view('processes.edit', compact('process'));
    }

    public function update(Request $request, Process $process)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:processes,name,' . $process->id,
            'active' => 'boolean'
        ]);

        $validated['active'] = $request->has('active');

        $process->update($validated);

        return redirect()
            ->route('processes.index')
            ->with('success', 'Proceso actualizado exitosamente.');
    }

    public function destroy(Request $request, Process $process)
    {
        try {
            $process->delete();
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => 'Proceso eliminado exitosamente.'
                ]);
            }

            return redirect()
                ->route('processes.index')
                ->with('success', 'Proceso eliminado exitosamente.');

        } catch (\Exception $e) {
            $errorMessage = 'No se puede eliminar el proceso porque está en uso.';
            
            if ($request->ajax()) {
                return response()->json([
                    'error' => $errorMessage
                ], 422);
            }

            return redirect()
                ->route('processes.index')
                ->with('error', $errorMessage);
        }
    }
}