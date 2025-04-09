<?php

namespace App\Http\Controllers;

use App\Models\Process;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProcessController extends Controller
{
    public function index(Request $request)
    {
        $query = Process::with(['leader', 'secondLeader']);

        // Búsqueda
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%")
                    ->orWhereHas('leader', function ($q) use ($searchTerm) {
                        $q->where('name', 'LIKE', "%{$searchTerm}%");
                    })
                    ->orWhereHas('secondLeader', function ($q) use ($searchTerm) {
                        $q->where('name', 'LIKE', "%{$searchTerm}%");
                    });
            });
        }

        // Filtro de estado
        if ($request->has('status') && $request->status !== '') {
            $query->where('active', $request->status);
        }

        // Filtro de líder
        if ($request->has('leader')) {
            if ($request->leader === 'with_leader') {
                $query->where(function ($q) {
                    $q->whereNotNull('leader_id')
                        ->orWhereNotNull('second_leader_id');
                });
            } elseif ($request->leader === 'without_leader') {
                $query->whereNull('leader_id')
                    ->whereNull('second_leader_id');
            }
        }

        // Ordenamiento
        $sortColumn = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');

        if (in_array($sortColumn, ['name', 'created_at'])) {
            $query->orderBy($sortColumn, $sortDirection);
        }

        // Paginación
        $processes = $query->paginate(10);
        $processes->appends($request->query());

        // Obtener todos los usuarios activos
        $users = User::where('active', true)->get();

        if ($request->ajax()) {
            return response()->json([
                'table' => view('processes._table_body', compact('processes', 'users'))->render(),
                'pagination' => view('pagination.tailwind', ['paginator' => $processes])->render()
            ]);
        }

        return view('processes.index', compact('processes', 'users'));
    }

    public function create()
    {
        // Obtener todos los usuarios activos
        $users = User::where('active', true)->get();
        return view('processes.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:processes',
            'active' => 'boolean',
            'leader_id' => 'nullable|exists:users,id',
            'second_leader_id' => 'nullable|exists:users,id|different:leader_id'
        ]);

        $validated['active'] = $request->has('active');

        try {
            DB::beginTransaction();
            Process::create($validated);
            DB::commit();

            return redirect()
                ->route('processes.index')
                ->with('success', 'Proceso creado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al crear el proceso: ' . $e->getMessage());
        }
    }

    public function edit(Process $process)
    {
        // Obtener todos los usuarios activos
        $users = User::where('active', true)->get();
        return view('processes.edit', compact('process', 'users'));
    }

    public function update(Request $request, Process $process)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:processes,name,' . $process->id,
            'active' => 'boolean',
            'leader_id' => 'nullable|exists:users,id',
            'second_leader_id' => 'nullable|exists:users,id|different:leader_id'
        ]);

        $validated['active'] = $request->has('active');

        try {
            DB::beginTransaction();
            $process->update($validated);
            DB::commit();

            return redirect()
                ->route('processes.index')
                ->with('success', 'Proceso actualizado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al actualizar el proceso: ' . $e->getMessage());
        }
    }

    public function destroy(Request $request, Process $process)
    {
        try {
            DB::beginTransaction();

            // Verificar si hay usuarios asociados
            if ($process->users()->exists()) {
                throw new \Exception('No se puede eliminar el proceso porque tiene usuarios asociados.');
            }

            $process->delete();
            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => 'Proceso eliminado exitosamente.'
                ]);
            }

            return redirect()
                ->route('processes.index')
                ->with('success', 'Proceso eliminado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            $errorMessage = $e->getMessage() ?: 'No se puede eliminar el proceso porque está en uso.';

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

    public function assignLeader(Request $request, Process $process)
    {
        $validated = $request->validate([
            'leader_id' => 'required|exists:users,id'
        ]);

        try {
            DB::beginTransaction();

            // Verificar que el líder principal no sea igual al secundario si existe
            if ($process->second_leader_id && $validated['leader_id'] == $process->second_leader_id) {
                throw new \Exception('El líder principal no puede ser el mismo que el líder secundario.');
            }

            $process->update(['leader_id' => $validated['leader_id']]);

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => 'Líder principal asignado exitosamente.'
                ]);
            }

            return redirect()
                ->route('processes.index')
                ->with('success', 'Líder principal asignado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            $errorMessage = $e->getMessage() ?: 'Error al asignar el líder principal.';

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

    public function assignSecondLeader(Request $request, Process $process)
    {
        $validated = $request->validate([
            'leader_id' => 'required|exists:users,id'
        ]);

        try {
            DB::beginTransaction();

            // Verificar que exista un líder principal asignado
            if (!$process->leader_id) {
                throw new \Exception('Debe asignar un líder principal antes de asignar un líder secundario.');
            }

            // Verificar que el líder secundario no sea igual al principal
            if ($validated['leader_id'] == $process->leader_id) {
                throw new \Exception('El líder secundario no puede ser el mismo que el líder principal.');
            }

            $process->update(['second_leader_id' => $validated['leader_id']]);

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => 'Líder secundario asignado exitosamente.'
                ]);
            }

            return redirect()
                ->route('processes.index')
                ->with('success', 'Líder secundario asignado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            $errorMessage = $e->getMessage() ?: 'Error al asignar el líder secundario.';

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

    public function getLeaderInfo(Process $process)
    {
        return response()->json([
            'leader' => $process->leader,
            'secondLeader' => $process->secondLeader
        ]);
    }

    public function removeLeader(Request $request, Process $process)
    {
        try {
            DB::beginTransaction();

            $process->update(['leader_id' => null]);

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => 'Líder principal removido exitosamente.'
                ]);
            }

            return redirect()
                ->route('processes.index')
                ->with('success', 'Líder principal removido exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            $errorMessage = $e->getMessage() ?: 'Error al remover el líder principal.';

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

    public function removeSecondLeader(Request $request, Process $process)
    {
        try {
            DB::beginTransaction();

            $process->update(['second_leader_id' => null]);

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => 'Líder secundario removido exitosamente.'
                ]);
            }

            return redirect()
                ->route('processes.index')
                ->with('success', 'Líder secundario removido exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            $errorMessage = $e->getMessage() ?: 'Error al remover el líder secundario.';

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
