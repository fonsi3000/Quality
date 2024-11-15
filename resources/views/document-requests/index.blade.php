@extends('layouts.app')

@section('title', 'Solicitudes de Documentos')

@section('content')

<div class="flex flex-col">
    <div class="overflow-x-auto min-h-[631px]">
        <div class="min-w-full inline-block align-middle">
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden dark:bg-neutral-800 dark:border-neutral-700">
                <!-- Header -->
                <div class="px-4 py-4 grid gap-3 md:flex md:justify-between md:items-center border-b border-gray-200 dark:border-neutral-700">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800 dark:text-neutral-200">
                            Solicitudes de Documentos
                        </h2>
                    </div>

                    <div class="sm:col-span-2 md:grow max-w-sm">
                        <div class="flex flex-col sm:flex-row gap-3">
                            <div class="grow">
                                <div class="relative">
                                    <input type="text" id="hs-table-search" class="py-2 px-3 ps-11 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-400" placeholder="Buscar solicitudes">
                                    <div class="absolute inset-y-0 start-0 flex items-center pointer-events-none ps-4">
                                        <svg class="size-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <a href="{{ route('documents.requests.create') }}" class="w-full sm:w-auto inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600 py-2 px-3">
                                    <svg class="flex-shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M14 3v4a1 1 0 0 0 1 1h4"/>
                                        <path d="M17 21H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h7l5 5v11a2 2 0 0 1-2 2z"/>
                                        <line x1="12" y1="11" x2="12" y2="17"/>
                                        <line x1="9" y1="14" x2="15" y2="14"/>
                                    </svg>
                                    Nueva Solicitud
                                </a>
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

                <!-- Tabla Responsive -->
                <div class="bg-white dark:bg-neutral-900 border border-gray-200 dark:border-neutral-700 rounded-xl">
                    <div class="max-h-[600px] overflow-y-auto">
                        <div class="overflow-x-auto">
                            <div class="inline-block min-w-full align-middle">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                                    <thead class="bg-gray-50 dark:bg-neutral-800 sticky top-0 z-10">
                                        <tr>
                                            <!-- Headers de columnas responsivas -->
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
                                                <!-- Celdas responsivas -->
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
                                                        <!-- Iconos de acción responsivos -->
                                                        <button type="button" 
                                                                data-hs-overlay="#request-modal-{{ $request->id }}"
                                                                class="text-gray-500 hover:text-gray-800 dark:text-neutral-400 dark:hover:text-neutral-300">
                                                            <svg class="size-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                                <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                                                                <circle cx="12" cy="12" r="3"/>
                                                            </svg>
                                                        </button>

                                                        <a href="{{ route('documents.requests.download', $request->id) }}" 
                                                           class="text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                                                            <svg class="size-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                                                <polyline points="7 10 12 15 17 10"/>
                                                                <line x1="12" x2="12" y1="15" y2="3"/>
                                                            </svg>
                                                        </a>

                                                        @if($request->canBeEdited())
                                                        <a href="{{ route('documents.requests.edit', $request->id) }}" 
                                                           class="text-yellow-500 hover:text-yellow-700 dark:text-yellow-400 dark:hover:text-yellow-300">
                                                            <svg class="size-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                                <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/>
                                                                <path d="m15 5 4 4"/>
                                                            </svg>
                                                        </a>
                                                        @endif
                                                        @can('admin.agent')
                                                        <form action="{{ route('documents.requests.destroy', $request->id) }}" 
                                                              method="POST" 
                                                              class="inline"
                                                              onsubmit="return confirm('¿Estás seguro de que deseas eliminar esta solicitud?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" 
                                                                    class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">
                                                                <svg class="size-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                                    <path d="M3 6h18"/>
                                                                    <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/>
                                                                    <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>
                                                                </svg>
                                                            </button>
                                                        </form>
                                                        @endcan
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Paginación Responsive -->
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
<!-- Modal Principal Responsive -->
<div id="request-modal-{{ $request->id }}" class="hs-overlay hidden w-full h-full fixed top-0 start-0 z-[60] overflow-x-hidden overflow-y-auto">
    <div class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all w-[95%] sm:w-[90%] md:w-[85%] m-3 mx-auto min-h-[calc(100%-3.5rem)] flex items-center justify-center">
        <div class="relative flex flex-col bg-white border shadow-sm rounded-xl w-full max-w-4xl dark:bg-neutral-800 dark:border-neutral-700">
            <!-- Header del Modal -->
            <div class="py-3 px-4 border-b dark:border-neutral-700">
                <div class="flex justify-between items-center">
                    <h3 class="font-bold text-gray-800 dark:text-white">
                        Detalles de la Solicitud #{{ $request->id }}
                    </h3>
                    <button type="button" 
                            class="inline-flex items-center justify-center size-8 text-sm font-semibold rounded-lg border border-transparent text-gray-800 hover:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:text-white dark:hover:bg-gray-700 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600"
                            data-hs-overlay="#request-modal-{{ $request->id }}">
                        <span class="sr-only">Cerrar</span>
                        <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                    </button>
                </div>
            </div>

            <!-- Body del Modal Responsive -->
            <div class="p-4 overflow-y-auto">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <!-- Columna Izquierda -->
                    <div class="space-y-4">
                        <!-- Información Principal -->
                        <div class="bg-gray-50 rounded-lg p-4 dark:bg-neutral-700">
                            <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-3">
                                Información Principal
                            </h4>
                            <div class="space-y-3">
                                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center">
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Estado:</span>
                                    <span class="inline-flex items-center gap-1.5 py-1 px-3 rounded-full text-sm font-medium mt-1 sm:mt-0 {{ $statusClasses[$request->status] }}">
                                        {{ $statusLabels[$request->status] }}
                                    </span>
                                </div>
                                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center">
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Tipo:</span>
                                    <span class="text-sm text-gray-800 dark:text-gray-200 mt-1 sm:mt-0">
                                        {{ $request->request_type === 'create' ? 'Nuevo Documento' : 'Modificación' }}
                                    </span>
                                </div>
                                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center">
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Documento:</span>
                                    <span class="text-sm text-gray-800 dark:text-gray-200 mt-1 sm:mt-0">
                                        {{ $request->document_name }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Información de Participantes -->                         
                        <div class="bg-gray-50 rounded-lg p-4 dark:bg-neutral-700">                             
                            <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-3">                                 
                                Participantes                             
                            </h4>                             
                            <div class="space-y-3">                                 
                                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center">                                     
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Solicitante:</span>                                     
                                    <span class="text-sm text-gray-800 dark:text-gray-200 mt-1 sm:mt-0">                                         
                                        {{ $request->user->name }}                                     
                                    </span>                                 
                                </div>                                 
                                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center">                                     
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Responsable:</span>                                     
                                    <span class="text-sm text-gray-800 dark:text-gray-200 mt-1 sm:mt-0">                                         
                                        {{ $request->responsible->name }}                                     
                                    </span>                                 
                                </div>
                                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center">                                     
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Agente Asignado:</span>                                     
                                    <span class="text-sm text-gray-800 dark:text-gray-200 mt-1 sm:mt-0">                                         
                                        @if($request->assigned_agent_id)
                                            {{ $request->assignedAgent->name }}
                                        @else
                                            <span class="text-gray-400 dark:text-neutral-500">Sin asignar</span>
                                        @endif                                    
                                    </span>                                 
                                </div>                                 
                                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center">                                     
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Origen:</span>                                     
                                    <span class="text-sm text-gray-800 dark:text-gray-200 mt-1 sm:mt-0">                                         
                                        {{ $request->origin }}                                     
                                    </span>                                 
                                </div>                                 
                                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center">                                     
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Destino:</span>                                     
                                    <span class="text-sm text-gray-800 dark:text-gray-200 mt-1 sm:mt-0">                                         
                                        {{ $request->destination }}                                     
                                    </span>                                 
                                </div>                             
                            </div>                         
                        </div>
                    </div>

                    <!-- Columna Derecha -->
                    <div class="space-y-4">
                        <!-- Detalles -->
                        <div class="bg-gray-50 rounded-lg p-4 dark:bg-neutral-700">
                            <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-3">
                                Detalles
                            </h4>
                            <div class="space-y-3">
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
                                        Observaciones:
                                    </span>
                                    <p class="text-sm text-gray-800 dark:text-gray-200 bg-white dark:bg-neutral-800 p-3 rounded-lg">
                                        {{ $request->observations }}
                                    </p>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Fechas -->
                        <div class="bg-gray-50 rounded-lg p-4 dark:bg-neutral-700">
                            <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-3">
                                Fechas
                            </h4>
                            <div class="space-y-3">
                                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center">
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Creación:</span>
                                    <span class="text-sm text-gray-800 dark:text-gray-200 mt-1 sm:mt-0">
                                        {{ $request->created_at->format('d/m/Y H:i') }}
                                    </span>
                                </div>
                                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center">
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Última actualización:</span>
                                    <span class="text-sm text-gray-800 dark:text-gray-200 mt-1 sm:mt-0">
                                        {{ $request->observations ? $request->updated_at->format('d/m/Y H:i') : 'Sin actualización' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer del Modal Responsive -->
            <div class="flex flex-col sm:flex-row justify-end items-center gap-3 py-3 px-4 border-t dark:border-neutral-700">
                <a href="{{ route('documents.requests.preview', $request->id) }}"
                   target="_blank"
                   class="w-full sm:w-auto py-2.5 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-lg border-2 border-blue-600 bg-blue-600 text-white hover:bg-blue-700 hover:border-blue-700 transition-colors duration-200">
                    <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                        <circle cx="12" cy="12" r="3"/>
                    </svg>
                    Ver Documento Borrador
                </a>
                @can('admin.agent')
                <button type="button"
                        data-hs-overlay="#assign-modal-{{ $request->id }}"
                        class="w-full sm:w-auto py-2.5 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-lg border-2 border-emerald-600 bg-emerald-600 text-white hover:bg-emerald-700 hover:border-emerald-700 transition-colors duration-200">
                    <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                    Aprobar y Asignar
                </button>

                <button type="button"
                        data-hs-overlay="#reject-modal-{{ $request->id }}"
                        class="w-full sm:w-auto py-2.5 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-lg border-2 border-red-600 bg-red-600 text-white hover:bg-red-700 hover:border-red-700 transition-colors duration-200">
                    <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 6 6 18"/><path d="m6 6 12 12"/>
                    </svg>
                    Rechazar
                </button>
                @endcan

                <button type="button"
                        data-hs-overlay="#request-modal-{{ $request->id }}"
                        class="w-full sm:w-auto py-2.5 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-lg border-2 border-gray-200 bg-white text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition-colors duration-200">
                    <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 6 6 18"/><path d="m6 6 12 12"/>
                    </svg>
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Rechazo Responsive -->
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
                            Rechazar Solicitud {{ $request->id }}
                        </h3>
                        <button type="button" 
                                class="inline-flex items-center justify-center size-8 text-sm font-semibold rounded-lg border border-transparent text-gray-800 hover:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:text-white dark:hover:bg-gray-700 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600"
                                data-hs-overlay="#reject-modal-{{ $request->id }}">
                            <span class="sr-only">Cerrar</span>
                            <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
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
                        <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 6 6 18"/><path d="m6 6 12 12"/>
                        </svg>
                        Confirmar Rechazo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de Asignación Responsive -->
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
                            <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
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
                            class="w-full sm:w-auto py-2 px-3 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-lg border border-indigo-500 bg-indigo-500 text-white hover:bg-indigo-600 hover:border-indigo-600 disabled:opacity-50 disabled:pointer-events-none">
                        <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                        Confirmar Asignación
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endforeach

<script>
function validateRejectForm(requestId) {
    const textarea = document.getElementById(`observations-${requestId}`);
    const errorDiv = document.getElementById(`error-observations-${requestId}`);
    
    errorDiv.textContent = '';
    errorDiv.classList.add('hidden');
    
    if (textarea.value.length < 10) {
        errorDiv.textContent = 'La observación debe tener al menos 10 caracteres';
        errorDiv.classList.remove('hidden');
        return false;
    }
    
    if (textarea.value.length > 1000) {
        errorDiv.textContent = 'La observación no puede exceder los 1000 caracteres';
        errorDiv.classList.remove('hidden');
        return false;
    }
    
    return true;
}
</script>
@endsection