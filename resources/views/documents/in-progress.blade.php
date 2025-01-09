@extends('layouts.app')

@section('title', 'Documentos en Elaboración')

@section('content')
<div class="flex flex-col">
    <div class="overflow-x-auto min-h-[631px]">
        <div class="min-w-full inline-block align-middle">
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden dark:bg-neutral-800 dark:border-neutral-700">
                <!-- Header -->
                <div class="px-4 py-4 grid gap-3 md:flex md:justify-between md:items-center border-b border-gray-200 dark:border-neutral-700">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800 dark:text-neutral-200">
                            Documentos en Elaboración
                        </h2>
                    </div>

                    <div class="sm:col-span-2 md:grow max-w-sm">
                        <div class="flex flex-col sm:flex-row gap-3">
                            <div class="grow">
                                <div class="relative">
                                    <input type="text" id="hs-table-search" class="py-2 px-3 ps-11 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-400" placeholder="Buscar documentos">
                                    <div class="absolute inset-y-0 start-0 flex items-center pointer-events-none ps-4">
                                        <svg class="size-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Alertas -->
                @if(session('success'))
                <div id="success-alert" class="mx-4 mt-4 bg-green-100 border border-green-200 text-sm text-green-800 rounded-lg p-4 dark:bg-green-800/10 dark:border-green-900 dark:text-green-500" role="alert">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="size-4 text-green-600" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 6 9 17l-5-5"/>
                            </svg>
                        </div>
                        <div class="ms-3">
                            <p class="font-medium">{{ session('success') }}</p>
                        </div>
                        <button type="button" class="ms-auto -mx-1.5" onclick="this.parentElement.parentElement.style.display='none'">
                            <span class="sr-only">Cerrar</span>
                            <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M18 6 6 18"/>
                                <path d="m6 6 12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
                @endif

                @if($errors->any())
                <div id="error-alert" class="mx-4 mt-4 bg-red-100 border border-red-200 text-sm text-red-800 rounded-lg p-4 dark:bg-red-800/10 dark:border-red-900 dark:text-red-500" role="alert">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="size-4 text-red-600" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"/>
                                <line x1="12" y1="8" x2="12" y2="12"/>
                                <line x1="12" y1="16" x2="12.01" y2="16"/>
                            </svg>
                        </div>
                        <div class="ms-3">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li class="font-medium">{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <button type="button" class="ms-auto -mx-1.5" onclick="this.parentElement.parentElement.style.display='none'">
                            <span class="sr-only">Cerrar</span>
                            <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M18 6 6 18"/>
                                <path d="m6 6 12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
                @endif

                <!-- Tabla -->
                <div class="bg-white dark:bg-neutral-900 border border-gray-200 dark:border-neutral-700 rounded-xl">
                    <div class="max-h-[600px] overflow-y-auto">
                        <div class="overflow-x-auto">
                            <div class="inline-block min-w-full align-middle">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                                    <thead class="bg-gray-50 dark:bg-neutral-800 sticky top-0 z-10">
                                        <tr>
                                            <th scope="col" class="px-4 py-3 text-start">
                                                <div class="flex items-center gap-x-2">
                                                    <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 dark:text-neutral-200">
                                                        Tipo/Nombre
                                                    </span>
                                                </div>
                                            </th>
                                            <th scope="col" class="hidden sm:table-cell px-4 py-3 text-start">
                                                <div class="flex items-center gap-x-2">
                                                    <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 dark:text-neutral-200">
                                                        Solicitante
                                                    </span>
                                                </div>
                                            </th>
                                            <th scope="col" class="hidden md:table-cell px-4 py-3 text-start">
                                                <div class="flex items-center gap-x-2">
                                                    <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 dark:text-neutral-200">
                                                        Responsable
                                                    </span>
                                                </div>
                                            </th>
                                            <th scope="col" class="hidden lg:table-cell px-4 py-3 text-start">
                                                <div class="flex items-center gap-x-2">
                                                    <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 dark:text-neutral-200">
                                                        Agente
                                                    </span>
                                                </div>
                                            </th>
                                            <th scope="col" class="px-4 py-3 text-start">
                                                <div class="flex items-center gap-x-2">
                                                    <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 dark:text-neutral-200">
                                                        Estado
                                                    </span>
                                                </div>
                                            </th>
                                            <th scope="col" class="hidden sm:table-cell px-4 py-3 text-start">
                                                <div class="flex items-center gap-x-2">
                                                    <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 dark:text-neutral-200">
                                                        Fecha
                                                    </span>
                                                </div>
                                            </th>
                                            <th scope="col" class="px-4 py-3 text-end sticky right-0 bg-gray-50 dark:bg-neutral-800">
                                                <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 dark:text-neutral-200">
                                                    Acciones
                                                </span>
                                            </th>
                                        </tr>
                                    </thead>

                                    <tbody class="divide-y divide-gray-200 dark:divide-neutral-700">
                                        @foreach($documentRequests as $request)
                                            <tr class="hover:bg-gray-50 dark:hover:bg-neutral-800">
                                                <td class="px-4 py-4">
                                                    <div class="flex flex-col">
                                                        <span class="text-sm font-medium text-gray-800 dark:text-neutral-200">
                                                            {{ $request->documentType->name }}
                                                        </span>
                                                        <span class="text-xs text-gray-500 dark:text-neutral-400">
                                                            {{ $request->document_name }}
                                                        </span>
                                                    </div>
                                                </td>
                                                <td class="hidden sm:table-cell px-4 py-4">
                                                    <div class="text-sm text-gray-800 dark:text-neutral-200">
                                                        {{ $request->user->name }}
                                                    </div>
                                                </td>
                                                <td class="hidden md:table-cell px-4 py-4">
                                                    <div class="text-sm text-gray-800 dark:text-neutral-200">
                                                        {{ $request->responsible->name }}
                                                    </div>
                                                </td>
                                                <td class="hidden lg:table-cell px-4 py-4">
                                                    <div class="text-sm text-gray-800 dark:text-neutral-200">
                                                        @if($request->assigned_agent_id)
                                                            {{ $request->assignedAgent->name }}
                                                        @else
                                                            <span class="text-gray-400 dark:text-neutral-500">Sin asignar</span>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="px-4 py-4">
                                                    <span class="inline-flex items-center gap-1.5 py-0.5 px-2 rounded-full text-xs font-medium 
                                                    @switch($request->status)
                                                        @case('sin_aprobar')
                                                            bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300
                                                            @break
                                                        @case('en_elaboracion')
                                                            bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300
                                                            @break
                                                        @case('revision')
                                                            bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300
                                                            @break
                                                        @case('publicado')
                                                            bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                                            @break
                                                        @case('rechazado')
                                                            bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300
                                                            @break
                                                        @default
                                                            bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300
                                                    @endswitch
                                                    ">
                                                        {{ $statusLabels[$request->status] ?? $request->status }}
                                                    </span>
                                                </td>
                                                <td class="hidden sm:table-cell px-4 py-4">
                                                    <div class="text-sm text-gray-800 dark:text-neutral-200">
                                                        {{ $request->created_at->format('d/m/Y H:i') }}
                                                    </div>
                                                </td>
                                                <td class="px-4 py-4 text-end sticky right-0 bg-white dark:bg-neutral-900">
                                                    <div class="flex items-center justify-end gap-x-3">
                                                        <!-- Botón de Ver -->
                                                        <button type="button" 
                                                                data-hs-overlay="#request-modal-{{ $request->id }}"
                                                                class="text-gray-500 hover:text-gray-800 dark:text-neutral-400 dark:hover:text-neutral-300">
                                                            <svg class="size-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                                <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                                                                <circle cx="12" cy="12" r="3"/>
                                                            </svg>
                                                        </button>

                                                        {{-- <!-- Botón de Descargar Original -->
                                                        <a href="{{ route('documents.requests.download', $request->id) }}" 
                                                           class="text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                                                            <svg class="size-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                                                <polyline points="7 10 12 15 17 10"/>
                                                                <line x1="12" x2="12" y1="15" y2="3"/>
                                                            </svg>
                                                        </a>

                                                        <!-- Botón de Adjuntar Documento Final -->
                                                        @if($request->status === 'en_elaboracion')
                                                        <button type="button"
                                                                data-hs-overlay="#attach-final-modal-{{ $request->id }}"
                                                                class="text-green-500 hover:text-green-700 dark:text-green-400 dark:hover:text-green-300">
                                                                <svg class="size-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                                    <path d="M12 5v14"/>
                                                                    <path d="M5 12h14"/>
                                                                </svg>
                                                            </button>
                                                            @endif
    
                                                            <!-- Botón de Descargar Documento Final (si existe) -->
                                                            @if($request->final_document_path)
                                                            <a href="{{ route('documents.requests.download-final', $request->id) }}" 
                                                               class="text-indigo-500 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300">
                                                                <svg class="size-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                                                    <polyline points="7 10 12 15 17 10"/>
                                                                    <line x1="12" x2="12" y1="15" y2="3"/>
                                                                </svg>
                                                            </a>
                                                            @endif --}}
                                                        </div>
                                                </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
    
                        <!-- Paginación -->
                        <div class="px-4 py-4 grid gap-3 md:flex md:justify-between md:items-center border-t border-gray-200 dark:border-neutral-700">
                            <div class="flex items-center gap-2">
                                <p class="text-sm text-gray-600 dark:text-neutral-400">
                                    Mostrando <span class="font-semibold text-gray-800 dark:text-neutral-200">{{ $documentRequests->firstItem() ?? 0 }}</span>
                                    a <span class="font-semibold text-gray-800 dark:text-neutral-200">{{ $documentRequests->lastItem() ?? 0 }}</span>
                                    de <span class="font-semibold text-gray-800 dark:text-neutral-200">{{ $documentRequests->total() }}</span> resultados
                                </p>
                            </div>
                            <div class="flex justify-center md:justify-end">
                                {{ $documentRequests->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modales -->
    @foreach($documentRequests as $request)
        <!-- Modal Principal -->
        <div id="request-modal-{{ $request->id }}" class="hs-overlay hidden w-full h-full fixed top-0 start-0 z-[60] overflow-x-hidden overflow-y-auto">
            <div class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all w-[95%] sm:w-[90%] md:w-[85%] m-3 mx-auto min-h-[calc(100%-3.5rem)] flex items-center justify-center">
                <div class="relative flex flex-col bg-white border shadow-sm rounded-xl w-full max-w-4xl dark:bg-neutral-800 dark:border-neutral-700">
                    <!-- Header del Modal -->
                    <div class="py-3 px-4 border-b dark:border-neutral-700">
                        <div class="flex justify-between items-center">
                            <h3 class="font-bold text-gray-800 dark:text-white">
                                Detalles del Documento #{{ $request->id }}
                            </h3>
                            <button type="button" 
                                    class="inline-flex items-center justify-center size-8 text-sm font-semibold rounded-lg border border-transparent text-gray-800 hover:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:text-white dark:hover:bg-gray-700 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600"
                                    data-hs-overlay="#request-modal-{{ $request->id }}">
                                <span class="sr-only">Cerrar</span>
                                <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                            </button>
                        </div>
                    </div>
    
                    <!-- Body del Modal -->
                    <<div class="p-4 overflow-y-auto">
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
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Tipo de Solicitud:</span>
                                            <span class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                                                {{ $request->getRequestTypeLabel() }}
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
        
                                    {{-- @can('admin.agent') --}}
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
                                    {{-- @endcan --}}
        
                                    <!-- Documento Final -->
                                    @if($request->final_document_path)
                                    <div>
                                        <h5 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                            Documento Final
                                            {{-- <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Publicado
                                            </span> --}}
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
        
                                    {{-- <!-- Historial de Versiones -->
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
                                    @endif --}}
                                </div>
                            </div>
                        </div>
                    </div>
    
                    
                    <<!-- Footer del Modal -->
                    <div class="flex flex-col sm:flex-row justify-end items-center gap-3 py-3 px-4 border-t dark:border-neutral-700">
                        <!-- Ver documento original -->
                        <a href="{{ route('documents.requests.preview', $request->id) }}"
                        target="_blank"
                        class="w-full sm:w-auto py-2.5 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 hover:text-gray-900 dark:bg-neutral-800 dark:border-neutral-700 dark:text-gray-300 dark:hover:bg-neutral-700 dark:hover:text-white transition-colors">
                            <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            Ver Borrador
                        </a>

                        <!-- Descargar documento original -->
                        <a href="{{ route('documents.requests.download', $request->id) }}"
                        class="w-full sm:w-auto py-2.5 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 hover:text-gray-900 dark:bg-neutral-800 dark:border-neutral-700 dark:text-gray-300 dark:hover:bg-neutral-700 dark:hover:text-white transition-colors">
                            <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Descargar Borrador
                        </a>

                        <!-- Adjuntar documento final (solo si está en elaboración) -->
                        @if($request->status === 'en_elaboracion')
                        <button type="button"
                                data-hs-overlay="#attach-final-modal-{{ $request->id }}"
                                class="w-full sm:w-auto py-2.5 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors">
                            <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14M5 12h14"/>
                            </svg>
                            Adjuntar Final
                        </button>
                        @endif

                        <!-- Descargar documento final (si existe) -->
                        @if($request->final_document_path)
                        <a href="{{ route('documents.requests.download-final', $request->id) }}"
                        class="w-full sm:w-auto py-2.5 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 hover:text-gray-900 dark:bg-neutral-800 dark:border-neutral-700 dark:text-gray-300 dark:hover:bg-neutral-700 dark:hover:text-white transition-colors">
                            <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Descargar Final
                        </a>
                        @endif

                        <!-- Botón Cerrar -->
                        <button type="button"
                                data-hs-overlay="#request-modal-{{ $request->id }}"
                                class="w-full sm:w-auto py-2.5 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                            <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    
        <!-- Modal para Adjuntar Documento Final -->
        @if($request->status === 'en_elaboracion')
        <div id="attach-final-modal-{{ $request->id }}" class="hs-overlay hidden w-full h-full fixed top-0 start-0 z-[70] overflow-x-hidden overflow-y-auto">
            <div class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all w-full max-w-lg mx-auto px-4 sm:px-6 py-3">
                <div class="relative flex flex-col bg-white shadow-lg rounded-xl dark:bg-neutral-800">
                    <div class="p-4 sm:p-6 text-center overflow-y-auto">
                        <h3 class="mb-2 text-xl font-bold text-gray-800 dark:text-gray-200">
                            Adjuntar Documento Final
                        </h3>
                        <form action="{{ route('documents.requests.attach-final', $request->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mt-4 space-y-4">
                                <div>
                                    <label class="block text-sm font-medium mb-2 dark:text-white">Documento Final</label>
                                    <input type="file" 
                                           name="final_document" 
                                           class="block w-full border border-gray-200 shadow-sm rounded-lg text-sm focus:z-10 focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-slate-900 dark:border-gray-700 dark:text-gray-400 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600
                                           file:bg-gray-50 file:border-0
                                           file:me-4
                                           file:py-3 file:px-4
                                           dark:file:bg-gray-700 dark:file:text-gray-400"
                                           required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-2 dark:text-white">Observaciones</label>
                                    <textarea name="observations" 
                                              class="resize-none py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-slate-900 dark:border-gray-700 dark:text-gray-400 dark:focus:ring-gray-600"
                                              rows="3"></textarea>
                                </div>
                            </div>
                            <div class="mt-6 flex justify-end gap-x-2">
                                <button type="button" 
                                        class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-slate-900 dark:border-gray-700 dark:text-white dark:hover:bg-gray-800 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600"
                                        data-hs-overlay="#attach-final-modal-{{ $request->id }}">
                                    Cancelar
                                </button>
                                <button type="submit" 
                                        class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600">
                                    <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 5v14"/>
                                        <path d="M5 12h14"/>
                                    </svg>
                                    Adjuntar y Enviar a Revisión
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endif
    @endforeach
    
    @endsection