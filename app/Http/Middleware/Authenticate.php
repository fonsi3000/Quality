<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verifica si el usuario no está autenticado
        if (!Auth::check()) {
            // Si la solicitud espera JSON, devuelve un error 401
            if ($request->expectsJson()) {
                return response()->json(['message' => 'No autenticado.'], 401);
            }

            // Si es una solicitud web, redirige al login
            return redirect('/');
        }

        // Verifica si el usuario está inactivo
        if (!Auth::user()->active) {
            // Cierra la sesión del usuario
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // Si la solicitud espera JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Tu cuenta está inactiva. Por favor, contacta al administrador.'
                ], 403);
            }

            // Si es una solicitud web
            return redirect('/')
                ->with('error', 'Tu cuenta está inactiva. Por favor, contacta al administrador.');
        }

        // Si el usuario está autenticado y activo, permite que la solicitud continúe
        return $next($request);
    }
}