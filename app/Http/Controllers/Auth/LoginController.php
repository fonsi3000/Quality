<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Verificar si el email existe
        $user = \App\Models\User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'errors' => [
                    'email' => ['No existe una cuenta con este correo electrónico.']
                ]
            ], 422);
        }

        // Verificar si el usuario está activo
        if (!$user->active) {
            return response()->json([
                'errors' => [
                    'email' => ['Tu cuenta está inactiva. Por favor, contacta al administrador.']
                ]
            ], 422);
        }

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Redirección basada en rol
            $redirectTo = $user->hasRole(['admin'])
                ? '/dashboard'
                : '/documents/published';

            return response()->json([
                'success' => true,
                'redirect' => $redirectTo
            ]);
        }

        return response()->json([
            'errors' => [
                'password' => ['La contraseña ingresada es incorrecta.']
            ]
        ], 422);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
