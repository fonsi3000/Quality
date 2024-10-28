<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Unit;
use App\Models\Process;
use App\Models\Position;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with(['unit', 'process', 'position'])
                    ->latest()
                    ->paginate(10);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $units = Unit::where('active', true)
                    ->orderBy('name')
                    ->get();
        $processes = Process::where('active', true)
                          ->orderBy('name')
                          ->get();
        $positions = Position::where('active', true)
                          ->orderBy('name')
                          ->get();

        return view('users.create', compact('units', 'processes', 'positions'));
    }

    public function store(UserRequest $request)
    {
        $validated = $request->validated();

        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('profile-photos', 'public');
            $validated['profile_photo'] = $path;
        }

        $validated['password'] = Hash::make($validated['password']);
        
        // No necesitas asignar explícitamente estos campos si ya están en el $validated
        // ya que deberían venir validados desde UserRequest

        User::create($validated);

        return redirect()
            ->route('users.index')
            ->with('success', 'Usuario creado exitosamente.');
    }

    public function edit(User $user)
    {
        $units = Unit::where('active', true)
                    ->orderBy('name')
                    ->get();
        $processes = Process::where('active', true)
                          ->orderBy('name')
                          ->get();
        $positions = Position::where('active', true)
                          ->orderBy('name')
                          ->get();

        return view('users.edit', compact('user', 'units', 'processes', 'positions'));
    }

    public function update(UserRequest $request, User $user)
    {
        $validated = $request->validated();

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

        $user->update($validated);

        return redirect()
            ->route('users.index')
            ->with('success', 'Usuario actualizado exitosamente.');
    }

    public function destroy(User $user)
    {
        // if (auth()->id() === $user->id) {
        //     return back()->with('error', 'No puedes eliminar tu propio usuario.');
        // }

        try {
            if ($user->profile_photo) {
                Storage::disk('public')->delete($user->profile_photo);
            }

            $user->delete();
            $message = ['success' => 'Usuario eliminado exitosamente.'];
        } catch (\Exception $e) {
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
            $message = ['success' => "Usuario {$status} exitosamente."];
        } catch (\Exception $e) {
            $message = ['error' => 'No se pudo cambiar el estado del usuario.'];
        }

        return redirect()
            ->route('users.index')
            ->with($message);
    }
}