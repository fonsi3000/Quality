<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Unit;
use App\Models\Process;
use App\Models\Position;
use App\Http\Requests\UserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    protected $roleNames = [
        'admin' => 'Líder de Calidad',
        'agent' => 'Auditor',
        'user' => 'Colaborador'
    ];

    public function index(Request $request)
    {
        $query = User::with(['unit', 'process', 'secondaryProcess','thirdProcess','fourthProcess','fifthProcess' ,'position', 'roles']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('active', $request->status === 'active');
        }

        if ($request->filled('role')) {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        if ($request->filled('unit')) {
            $query->where('unit_id', $request->unit);
        }

        if ($request->filled('process')) {
            $query->where(function ($q) use ($request) {
                $q->where('process_id', $request->process)
                    ->orWhere('second_process_id', $request->process);
            });
        }

        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        $users = $query->paginate(10);
        $users->appends($request->except('page'));

        $units = Unit::where('active', true)->orderBy('name')->get();
        $processes = Process::where('active', true)->orderBy('name')->get();
        $roles = collect($this->roleNames)->map(fn($name, $key) => (object)['name' => $key, 'display_name' => $name]);

        return view('users.index', compact('users', 'units', 'processes', 'roles'));
    }

    public function create()
    {
        $units = Unit::where('active', true)->orderBy('name')->get();
        $processes = Process::where('active', true)->orderBy('name')->get();
        $positions = Position::where('active', true)->orderBy('name')->get();
        $roles = Role::orderBy('name')->get();
        $roleNames = $this->roleNames;

        return view('users.create', compact('units', 'processes', 'positions', 'roles', 'roleNames'));
    }

    public function store(UserRequest $request)
    {
        $validated = $request->validated();

        Log::info('Datos validados al crear usuario:', $validated);

        $process_ids = [
            "process_id",
            "second_process_id",
            "third_process_id",
            "fourth_process_id",
            "fifth_process_id"
        ];

        $someEqual = false;
        foreach ($process_ids as $pid) {
            foreach($process_ids as $pid2){
            if ($pid != $pid2){
                if(isset($validated[$pid]) && isset($validated[$pid2])){
                    if ($validated[$pid] == $validated[$pid2]){
                        $someEqual = true;
                        break;
                    }
                }
            }
        }
        }

        if (
            $someEqual
        ) {
            return back()->withErrors(['process_id' => 'Cada proceso debe ser diferente del anterior.'])->withInput();
        }

        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('profile-photos', 'public');
            $validated['profile_photo'] = $path;
        }

        $validated['password'] = Hash::make($validated['password']);

        try {
            $user = User::create($validated);
            Log::info('Usuario creado:', ['user_id' => $user->id]);

            if ($request->has('role')) {
                $user->assignRole($request->role);
            }

            return redirect()
                ->route('users.index')
                ->with('success', 'Usuario creado exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error al crear usuario: ' . $e->getMessage(), $validated);
            return back()->with('error', 'No se pudo crear el usuario.')->withInput();
        }
    }

    public function edit(User $user)
    {
        $units = Unit::where('active', true)->orderBy('name')->get();
        $processes = Process::where('active', true)->orderBy('name')->get();
        $positions = Position::where('active', true)->orderBy('name')->get();
        $roles = Role::orderBy('name')->get();
        $userRole = $user->roles->first();
        $roleNames = $this->roleNames;

        return view('users.edit', compact('user', 'units', 'processes', 'positions', 'roles', 'userRole', 'roleNames'));
    }

    public function update(UserRequest $request, User $user)
    {
        $validated = $request->validated();

        Log::info('Datos validados al actualizar usuario:', array_merge(['user_id' => $user->id], $validated));

        $process_ids = [
            "process_id",
            "second_process_id",
            "third_process_id",
            "fourth_process_id",
            "fifth_process_id"
        ];

        $someEqual = false;
        foreach ($process_ids as $pid) {
            foreach($process_ids as $pid2){
            if ($pid != $pid2){
                if(isset($validated[$pid]) && isset($validated[$pid2])){
                    if ($validated[$pid] == $validated[$pid2]){
                        $someEqual = true;
                        break;
                    }
                }
            }
        }
        }

        if (
            $someEqual
        ) {
            return back()->withErrors(['process_id' => 'Cada proceso debe ser diferente del anterior.'])->withInput();
        }

        if ($request->hasFile('profile_photo')) {
            if ($user->profile_photo) {
                Storage::disk('public')->delete($user->profile_photo);
            }
            $path = $request->file('profile_photo')->store('profile-photos', 'public');
            $validated['profile_photo'] = $path;
        }

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        try {
            $user->update($validated);
            Log::info('Usuario actualizado correctamente.', ['user_id' => $user->id]);

            if ($request->has('role')) {
                $user->syncRoles([$request->role]);
            }

            return redirect()
                ->route('users.index')
                ->with('success', 'Usuario actualizado exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error al actualizar usuario: ' . $e->getMessage(), $validated);
            return back()->with('error', 'No se pudo actualizar el usuario.')->withInput();
        }
    }

    public function destroy(User $user)
    {
        if (auth()->id() === $user->id) {
            return back()->with('error', 'No puedes eliminar tu propio usuario.');
        }

        try {
            if ($user->profile_photo) {
                Storage::disk('public')->delete($user->profile_photo);
            }
            $user->delete();
            Log::info('Usuario eliminado', ['user_id' => $user->id]);
            $message = ['success' => 'Usuario eliminado exitosamente.'];
        } catch (\Exception $e) {
            Log::error('Error al eliminar usuario: ' . $e->getMessage());
            $message = ['error' => 'No se pudo eliminar el usuario.'];
        }

        return redirect()
            ->route('users.index')
            ->with($message);
    }

    public function toggleActive(User $user)
    {
        try {
            $user->update(['active' => !$user->active]);
            $status = $user->active ? 'activado' : 'desactivado';
            Log::info("Usuario {$status}", ['user_id' => $user->id]);
            $message = ['success' => "Usuario {$status} exitosamente."];
        } catch (\Exception $e) {
            Log::error('Error al cambiar estado del usuario: ' . $e->getMessage());
            $message = ['error' => 'No se pudo cambiar el estado del usuario.'];
        }

        return redirect()
            ->route('users.index')
            ->with($message);
    }

    public function getRoleFriendlyName($roleName)
    {
        return $this->roleNames[$roleName] ?? $roleName;
    }
}
