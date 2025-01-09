<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\DocumentRequest;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Conteo de usuarios activos e inactivos (mantener original)
        $activeUsersCount = User::where('active', true)->count();
        $inactiveUsersCount = User::where('active', false)->count();

        // Obtener usuario actual para las tareas
        $usuarioActual = Auth::user();

        // Conteo específico de tareas para el usuario actual
        $activeTasks = DocumentRequest::where(function ($query) use ($usuarioActual) {
            // Documentos donde es responsable (sin aprobar o en revisión)
            $query->where('responsible_id', $usuarioActual->id)
                ->whereIn('status', [
                    DocumentRequest::STATUS_SIN_APROBAR,
                    DocumentRequest::STATUS_REVISION
                ]);
        })
            ->orWhere(function ($query) use ($usuarioActual) {
                // Documentos donde es agente asignado (en elaboración)
                $query->where('assigned_agent_id', $usuarioActual->id)
                    ->where('status', DocumentRequest::STATUS_EN_ELABORACION);
            })
            ->count();

        // Conteo de documentos publicados (mantener original)
        $publishedDocuments = DocumentRequest::where(
            'status',
            DocumentRequest::STATUS_PUBLICADO
        )->count();

        return view('dashboard', compact(
            'activeUsersCount',
            'inactiveUsersCount',
            'activeTasks',
            'publishedDocuments'
        ));
    }
}
