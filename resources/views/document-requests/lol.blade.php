@foreach($documentRequests as $request)
    <!-- Modal Principal -->
    <div id="request-modal-{{ $request->id }}" class="hs-overlay hidden w-full h-full fixed top-0 start-0 z-[60] overflow-x-hidden overflow-y-auto">
        <div class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all w-[95%] sm:w-[90%] md:w-[85%] m-3 mx-auto min-h-[calc(100%-3.5rem)] flex items-center justify-center">
            <div class="relative flex flex-col bg-white border shadow-sm rounded-xl w-full max-w-4xl dark:bg-neutral-800 dark:border-neutral-700">
                <!-- Header del Modal -->
                <div class="py-3 px-4 border-b dark:border-neutral-700 bg-gray-50 dark:bg-neutral-700">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <h3 class="text-xl font-bold text-gray-800 dark:text-white">
                                {{ $request->document_name }}
                            </h3>
                            <span class="px-3 py-1 text-sm font-medium rounded-full {{ $request->isPublicado() ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                v{{ $request->version }}
                            </span>
                        </div>
                        <button type="button" 
                                class="inline-flex items-center justify-center size-8 text-sm font-semibold rounded-lg border border-transparent text-gray-800 hover:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:text-white dark:hover:bg-gray-700"
                                data-hs-overlay="#request-modal-{{ $request->id }}">
                            <span class="sr-only">Cerrar</span>
                            <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Body del Modal -->
                <div class="p-4 overflow-y-auto">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <!-- Columna Izquierda -->
                        <div class="space-y-4">
                            <!-- Información Principal -->
                            <div class="bg-gray-50 rounded-lg p-4 dark:bg-neutral-700">
                                <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-3 flex items-center gap-2">
                                    <svg class="size-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Información Principal
                                </h4>
                                <div class="space-y-3">
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Estado:</span>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ match($request->status) {
                                            'publicado' => 'bg-green-100 text-green-800',
                                            'rechazado', 'rechazado_lider' => 'bg-red-100 text-red-800',
                                            'sin_aprobar', 'pendiente_lider' => 'bg-yellow-100 text-yellow-800',
                                            'en_elaboracion' => 'bg-blue-100 text-blue-800',
                                            'revision' => 'bg-purple-100 text-purple-800',
                                            'obsoleto' => 'bg-gray-100 text-gray-800',
                                            default => 'bg-gray-100 text-gray-800'
                                        } }}">
                                            {{ $request->getStatusLabel() }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Proceso:</span>
                                        <span class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                                            {{ $request->process->name ?? 'No asignado' }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Tipo de Documento:</span>
                                        <span class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                                            {{ $request->documentType->name }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Información de Participantes -->                         
                            <div class="bg-gray-50 rounded-lg p-4 dark:bg-neutral-700">                             
                                <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-3 flex items-center gap-2">
                                    <svg class="size-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                    </svg>
                                    Participantes                             
                                </h4>                             
                                <div class="space-y-3">                                 
                                    <div class="flex justify-between items-center">                                     
                                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Solicitante:</span>                                     
                                        <span class="text-sm font-semibold text-gray-800 dark:text-gray-200">                                         
                                            {{ $request->user->name }}                                     
                                        </span>                                 
                                    </div>                                 
                                    <div class="flex justify-between items-center">                                     
                                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Responsable:</span>                                     
                                        <span class="text-sm font-semibold text-gray-800 dark:text-gray-200">                                         
                                            {{ $request->responsible->name ?? 'No asignado' }}                                     
                                        </span>                                 
                                    </div>
                                    <div class="flex justify-between items-center">                                     
                                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Agente Asignado:</span>                                     
                                        <span class="text-sm font-semibold text-gray-800 dark:text-gray-200">                                         
                                            {{ $request->assignedAgent?->name ?? 'No asignado' }}                                   
                                        </span>                                 
                                    </div>                                                              
                                </div>                         
                            </div>

                            <!-- Origen y Destino -->
                            <div class="bg-gray-50 rounded-lg p-4 dark:bg-neutral-700">
                                <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-3">
                                    Ubicación
                                </h4>
                                <div class="space-y-3">
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Origen:</span>
                                        <span class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                                            {{ $request->origin }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Destino:</span>
                                        <span class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                                            {{ $request->destination }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Columna Derecha -->
                        <div class="space-y-4">
                            <!-- Descripción y Observaciones -->
                            <div class="bg-gray-50 rounded-lg p-4 dark:bg-neutral-700">
                                <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-3">Detalles</h4>
                                <div class="space-y-4">
                                    <div>
                                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400 block mb-2">
                                            Descripción:
                                        </span>
                                        <p class="text-sm text-gray-800 dark:text-gray-200 bg-white dark:bg-neutral-800 p-3 rounded-lg">
                                            {{ $request->description ?: 'Sin descripción' }}
                                        </p>
                                    </div>
                                    @if($request->observations)
                                    <div>
                                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400 block mb-2">
                                            Observaciones de Calidad:
                                        </span>
                                        <p class="text-sm text-gray-800 dark:text-gray-200 bg-white dark:bg-neutral-800 p-3 rounded-lg">
                                            {{ $request->observations }}
                                        </p>
                                    </div>
                                    @endif
                                    @if($request->leader_observations)
                                    <div>
                                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400 block mb-2">
                                            Observaciones del Líder:
                                        </span>
                                        <p class="text-sm text-gray-800 dark:text-gray-200 bg-white dark:bg-neutral-800 p-3 rounded-lg">
                                            {{ $request->leader_observations }}
                                        </p>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Fechas -->
                            <div class="bg-gray-50 rounded-lg p-4 dark:bg-neutral-700">
                                <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-3 flex items-center gap-2">
                                    <svg class="size-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    Fechas Importantes
                                </h4>
                                <div class="space-y-3">
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Creación:</span>
                                        <span class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                                            {{ $request->created_at->format('d/m/Y H:i') }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Última actualización:</span>
                                        <span class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                                            {{ $request->updated_at->format('d/m/Y H:i') }}
                                        </span>
                                    </div>
                                    @if($request->leader_approval_date)
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Aprobación del Líder:</span>
                                        <span class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                                            {{ $request->leader_approval_date->format('d/m/Y H:i') }}
                                        </span>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Documentos -->
                            <div class="bg-gray-50 rounded-lg p-4 dark:bg-neutral-700">
                                <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-3 flex items-center gap-2">
                                    <svg class="size-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    Documentos
                                </h4>

                                @can('admin.agent')
                                <!-- Documento Borrador -->
                                <div class="mb-4">
                                    <h5 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                        Documento Borrador
                                    </h5>
                                    <div class="flex items-center gap-3">
                                        <a href="{{ route('documents.requests.preview', $request->id) }}"
                                           target="_blank"
                                           class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 hover:text-gray-900 dark:bg-neutral-800 dark:border-neutral-700 dark:text-gray-300 dark:hover:bg-neutral-700 dark:hover:text-white transition-colors">
                                            <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            Visualizar
                                        </a>
                                        <a href="{{ route('documents.requests.download', $request->id) }}"
                                           class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 hover:text-gray-900 dark:bg-neutral-800 dark:border-neutral-700 dark:text-gray-300 dark:hover:bg-neutral-700 dark:hover:text-white transition-colors">
                                            <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                            </svg>
                                            Descargar
                                        </a>
                                    </div>
                                </div>
                                @endcan

                                <!-- Documento Final -->
                                @if($request->final_document_path)
                                <div>
                                    <h5 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                        Documento Final
                                        <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Publicado
                                        </span>
                                    </h5>
                                    <div class="flex items-center gap-3">
                                        <a href="{{ route('documents.requests.preview-final', $request->id) }}"
                                           target="_blank"
                                           class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 hover:text-gray-900 dark:bg-neutral-800 dark:border-neutral-700 dark:text-gray-300 dark:hover:bg-neutral-700 dark:hover:text-white transition-colors">
                                            <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            Visualizar
                                        </a>
                                        <a href="{{ route('documents.requests.download-final', $request->id) }}"
                                           class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 hover:text-gray-900 dark:bg-neutral-800 dark:border-neutral-700 dark:text-gray-300 dark:hover:bg-neutral-700 dark:hover:text-white transition-colors">
                                            <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                            </svg>
                                            Descargar
                                        </a>
                                    </div>
                                </div>
                                @endif

                                <!-- Historial de Versiones -->
                                @if($request->hasReferenceDocument() || $request->hasReferencingDocuments())
                                <div class="mt-4">
                                    <h5 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                        Historial de Versiones
                                    </h5>
                                    <div class="space-y-3">
                                        @foreach($request->getVersionHistory() as $version)
                                        <div class="p-3 rounded-lg bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700">
                                            <div class="flex items-center justify-between">
                                                <div class="flex flex-col">
                                                    <span class="text-sm font-medium text-gray-800 dark:text-gray-200">
                                                        Versión {{ $version->version }}
                                                    </span>
                                                    <span class="text-xs text-gray-500">
                                                        {{ $version->created_at->format('d/m/Y H:i') }}
                                                    </span>
                                                </div>
                                                <div class="flex items-center gap-3">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ match($version->status) {
                                                        'publicado' => 'bg-green-100 text-green-800',
                                                        'rechazado', 'rechazado_lider' => 'bg-red-100 text-red-800',
                                                        'sin_aprobar', 'pendiente_lider' => 'bg-yellow-100 text-yellow-800',
                                                        'en_elaboracion' => 'bg-blue-100 text-blue-800',
                                                        'revision' => 'bg-purple-100 text-purple-800',
                                                        'obsoleto' => 'bg-gray-100 text-gray-800',
                                                        default => 'bg-gray-100 text-gray-800'
                                                    } }}">
                                                        {{ $version->getStatusLabel() }}
                                                    </span>
                                                    <a href="{{ route('documents.requests.preview', $version->id) }}"
                                                       target="_blank"
                                                       class="text-blue-600 hover:text-blue-700 dark:text-blue-500 dark:hover:text-blue-400">
                                                        <svg class="size-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                        </svg>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer del Modal -->
                <div class="flex flex-col sm:flex-row justify-end items-center gap-3 py-3 px-4 border-t dark:border-neutral-700">
                    @can('admin.agent')
                    <a href="{{ route('documents.requests.preview', $request->id) }}"
                       target="_blank"
                       class="w-full sm:w-auto py-2.5 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 hover:text-gray-900 dark:bg-neutral-800 dark:border-neutral-700 dark:text-gray-300 dark:hover:bg-neutral-700 dark:hover:text-white transition-colors">
                        <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        Ver Documento
                    </a>

                    <button type="button"
                            data-hs-overlay="#assign-modal-{{ $request->id }}"
                            class="w-full sm:w-auto py-2.5 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg transition-colors duration-200">
                        <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Aprobar y Asignar
                    </button>

                    <button type="button"
                            data-hs-overlay="#reject-modal-{{ $request->id }}"
                            class="w-full sm:w-auto py-2.5 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors duration-200">
                        <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Rechazar
                    </button>
                    @endcan

                    <button type="button"
                            data-hs-overlay="#request-modal-{{ $request->id }}"
                            class="w-full sm:w-auto py-2.5 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                        <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Rechazo -->
    <div id="reject-modal-{{ $request->id }}" class="hs-overlay hidden w-full h-full fixed top-0 start-0 z-[70] overflow-x-hidden overflow-y-auto">
        <div class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all w-[95%] sm:max-w-lg sm:w-full m-3 mx-auto min-h-[calc(100%-3.5rem)] flex items-center">
            <div class="relative flex flex-col bg-white border shadow-sm rounded-xl w-full dark:bg-neutral-800 dark:border-neutral-700">
                <form action="{{ route('documents.requests.reject', $request->id) }}" 
                    method="POST" 
                    id="reject-form-{{ $request->id }}"
                    onsubmit="return validateRejectForm({{ $request->id }})">
                    @csrf
                    @method('PUT')
                    
                    <!-- Header -->
                    <div class="py-3 px-4 border-b dark:border-neutral-700">
                        <div class="flex justify-between items-center">
                            <h3 class="font-bold text-gray-800 dark:text-white">
                                Rechazar Solicitud #{{ $request->id }}
                            </h3>
                            <button type="button" 
                                    class="inline-flex items-center justify-center size-8 text-sm font-semibold rounded-lg border border-transparent text-gray-800 hover:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:text-white dark:hover:bg-gray-700 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600"
                                    data-hs-overlay="#reject-modal-{{ $request->id }}">
                                <span class="sr-only">Cerrar</span>
                                <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Body -->
                    <div class="p-4">
                        <div class="space-y-4">
                            <div>
                                <label for="observations-{{ $request->id }}" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                                    Observaciones del Rechazo
                                </label>
                                <textarea
                                    id="observations-{{ $request->id }}"
                                    name="observations"
                                    rows="4"
                                    class="mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 dark:bg-neutral-700 dark:border-neutral-600 dark:text-white sm:text-sm"
                                    placeholder="Ingrese el motivo del rechazo..."
                                    required
                                    minlength="10"
                                    maxlength="1000"
                                ></textarea>
                                <div id="error-observations-{{ $request->id }}" class="mt-2 text-sm text-red-600 dark:text-red-400 hidden"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="flex flex-col-reverse sm:flex-row justify-end items-center gap-3 py-3 px-4 border-t dark:border-neutral-700">
                        <button type="button" 
                                class="w-full sm:w-auto py-2 px-3 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:text-white dark:hover:bg-neutral-700"
                                data-hs-overlay="#reject-modal-{{ $request->id }}">
                            Cancelar
                        </button>
                        <button type="submit"
                                class="w-full sm:w-auto py-2 px-3 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-lg border border-red-500 bg-red-500 text-white hover:bg-red-600 hover:border-red-600 disabled:opacity-50 disabled:pointer-events-none">
                            <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Confirmar Rechazo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal de Asignación -->
    <div id="assign-modal-{{ $request->id }}" class="hs-overlay hidden w-full h-full fixed top-0 start-0 z-[70] overflow-x-hidden overflow-y-auto">
        <div class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all w-[95%] sm:max-w-lg sm:w-full m-3 mx-auto min-h-[calc(100%-3.5rem)] flex items-center">
            <div class="relative flex flex-col bg-white border shadow-sm rounded-xl w-full dark:bg-neutral-800 dark:border-neutral-700">
                <form action="{{ route('documents.requests.assign', $request->id) }}" 
                    method="POST" 
                    id="assign-form-{{ $request->id }}">
                    @csrf
                    @method('PUT')
                    
                    <!-- Header -->
                    <div class="py-3 px-4 border-b dark:border-neutral-700">
                        <div class="flex justify-between items-center">
                            <h3 class="font-bold text-gray-800 dark:text-white">
                                Asignar Agente de Calidad
                            </h3>
                            <button type="button" 
                                    class="inline-flex items-center justify-center size-8 text-sm font-semibold rounded-lg border border-transparent text-gray-800 hover:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:text-white dark:hover:bg-gray-700"
                                    data-hs-overlay="#assign-modal-{{ $request->id }}">
                                <span class="sr-only">Cerrar</span>
                                <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Body -->
                    <div class="p-4">
                        <div class="space-y-4">
                            <div>
                                <label for="assigned_agent_id-{{ $request->id }}" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                                    Seleccionar Agente de Calidad
                                </label>
                                <select name="assigned_agent_id" 
                                        id="assigned_agent_id-{{ $request->id }}" 
                                        class="mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-neutral-700 dark:border-neutral-600 dark:text-white sm:text-sm"
                                        required>
                                    <option value="">Seleccione un agente de calidad</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ $request->assigned_agent_id == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="flex flex-col-reverse sm:flex-row justify-end items-center gap-3 py-3 px-4 border-t dark:border-neutral-700">
                        <button type="button"
                                class="w-full sm:w-auto py-2 px-3 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:text-white dark:hover:bg-neutral-700"
                                data-hs-overlay="#assign-modal-{{ $request->id }}">
                            Cancelar
                        </button>
                        <button type="submit"
                                class="w-full sm:w-auto py-2 px-3 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-lg border border-emerald-500 bg-emerald-500 text-white hover:bg-emerald-600 hover:border-emerald-600 disabled:opacity-50 disabled:pointer-events-none">
                            <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Confirmar Asignación
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach