<?php

namespace App\Http\Controllers;

use App\Models\Process;
use Illuminate\Http\Request;

class ProcessController extends Controller
{
    public function index()
    {
        $processes = Process::orderBy('created_at', 'desc')->get();
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
        ]);

        // Aseguramos que active sea true por defecto
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
        ]);

        // Aseguramos que active sea false si no viene en el request
        $validated['active'] = $request->has('active');

        $process->update($validated);

        return redirect()
            ->route('processes.index')
            ->with('success', 'Proceso actualizado exitosamente.');
    }

    public function destroy(Process $process)
    {
        try {
            $process->delete();
            $message = ['success' => 'Proceso eliminado exitosamente.'];
        } catch (\Exception $e) {
            $message = ['error' => 'No se puede eliminar el proceso porque está en uso.'];
        }

        return redirect()
            ->route('processes.index')
            ->with($message);
    }
}