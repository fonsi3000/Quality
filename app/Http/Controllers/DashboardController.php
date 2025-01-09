<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\DocumentRequest;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Obtener el usuario actual
        $usuarioActual = Auth::user();

        // Conteo de usuarios activos e inactivos
        $usuariosActivos = User::where('active', true)->count();
        $usuariosInactivos = User::where('active', false)->count();

        // Conteo de tareas para el usuario responsable
        $activeTasks = DocumentRequest::where('responsible_id', $usuarioActual->id)
            ->whereIn('status', [
                DocumentRequest::STATUS_SIN_APROBAR,
                DocumentRequest::STATUS_REVISION
            ])
            ->count();

        // Si el usuario es agente asignado, sumar también las tareas en elaboración
        $activeTasks += DocumentRequest::where('assigned_agent_id', $usuarioActual->id)
            ->where('status', DocumentRequest::STATUS_EN_ELABORACION)
            ->count();

        // Conteo de documentos publicados
        $documentosPublicados = DocumentRequest::where(
            'status',
            DocumentRequest::STATUS_PUBLICADO
        )->count();

        return view('dashboard', compact(
            'usuariosActivos',
            'usuariosInactivos',
            'activeTasks',
            'documentosPublicados'
        ));
    }
}
