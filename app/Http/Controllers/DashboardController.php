<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\DocumentRequest;
use App\Models\Process;
use Illuminate\Support\Facades\Auth;

use function PHPSTORM_META\map;

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

        
        //procesos con documentos publicados
        $process = Process::with(['documentRequests'])->whereHas('documentRequests',function($q){
            $q -> where('status','publicado');
        });     
        
        // traer id de la organizacion del usuario actual
        $unitId = $usuarioActual -> unit_id;
        // traer el id de los procesos de la organizacion a la que pertenece el usuario
        $processIds = User::where('unit_id', $unitId)->pluck('process_id')->unique()->toArray();

        // traer los primeros 4 ids de los procesos
        $mainIds = $process->whereIn('id', $processIds)->limit(4)->pluck('id')->toArray();
        
        // traer los primeros 4 procesos que se van a mostrar en el dashboard
        $mainProcess = $process->whereIn('id',$mainIds)->get();
        // traer los otros procesos
        $otherProcess = $process ->whereNotIn('id',$mainIds)->get();
        
        return view('dashboard', compact(
            'activeUsersCount',
            'inactiveUsersCount',
            'activeTasks',
            'publishedDocuments',
            'mainProcess',
            'otherProcess'
        ));
    }
}
