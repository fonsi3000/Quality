<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\DocumentRequest;

class DashboardController extends Controller
{
    public function index()
    {
        // Conteo de usuarios activos e inactivos
        $activeUsersCount = User::where('active', true)->count();
        $inactiveUsersCount = User::where('active', false)->count();

        // Conteo de tareas activas
        $activeTasks = DocumentRequest::whereNotIn('status', [
            DocumentRequest::STATUS_PUBLICADO,
            DocumentRequest::STATUS_RECHAZADO
        ])->count();

        // Conteo de documentos publicados
        $publishedDocuments = DocumentRequest::where('status', 
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