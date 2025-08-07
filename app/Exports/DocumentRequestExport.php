<?php

namespace App\Exports;

use App\Models\DocumentRequest;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DocumentRequestExport implements FromView
{
    //funcion para obtener reporte a partir de una vista
    /**
     * @return \Illuminate\Support\View
     */
    public function view(): View
    {
        $documentRequests = DocumentRequest::with(['process', 'documentType'])->whereHas('process', function ($q) {
            $usuarioActual = Auth::user();
            // traer id de la organizacion del usuario actual
            $unitId = $usuarioActual->unit_id;
            // traer el id de los procesos de la organizacion a la que pertenece el usuario
            $processIds = User::where('unit_id', $unitId)->pluck('process_id')->unique()->toArray();
            //procesos con documentos publicados
            $q->whereIn('status', [DocumentRequest::STATUS_OBSOLETO, DocumentRequest::STATUS_PUBLICADO])->whereIn('id', $processIds);
        })->get();

        return view('exportDocuments', ['report' => $documentRequests]);
    }
}
