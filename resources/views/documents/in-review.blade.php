@extends('layouts.app')

@section('title', 'Documentos')

@section('content')
<div class="flex flex-col">
    <div class="overflow-x-auto min-h-[631px]">
        <div class="min-w-full inline-block align-middle">
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden dark:bg-neutral-800 dark:border-neutral-700">
                <!-- Header -->
                <div class="px-4 py-4 grid gap-3 md:flex md:justify-between md:items-center border-b border-gray-200 dark:border-neutral-700">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800 dark:text-neutral-200">
                            Documentos
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
                                                    <span class="inline-flex items-center gap-1.5 py-0.5 px-2 rounded-full text-xs font-medium {{ $statusClasses[$request->status] }}">
                                                        {{ $statusLabels[$request->status] }}
                                                    </span>
                                                </td>
                                                <td class="hidden sm:table-cell px-4 py-4">
                                                    <div class="text-sm text-gray-800 dark:text-neutral-200">
                                                        {{ $request->created_at->format('d/m/Y H:i') }}
                                                    </div>
                                                </td>
                                                <td class="px-4 py-4 text-end sticky right-0 bg-white dark:bg-neutral-900">
                                                    <div class="flex items-center justify-end">
                                                        <!-- Botón de Ver -->
                                                        <button type="button" 
                                                                data-hs-overlay="#request-modal-{{ $request->id }}"
                                                                class="text-gray-500 hover:text-gray-800 dark:text-neutral-400 dark:hover:text-neutral-300">
                                                            <svg class="size-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                                <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                                                                <circle cx="12" cy="12" r="3"/>
                                                            </svg>
                                                        </button>
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
                                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center">
                                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Tipo de Documento:</span>
                                            <span class="text-sm text-gray-800 dark:text-gray-200 mt-1 sm:mt-0">
                                                {{ $request->documentType->name }}
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
                                    </div>
                                </div>
                            </div>

                            <!-- Columna Derecha -->
                            <div class="space-y-4">
                                <!-- Documentos -->
                                <div class="bg-gray-50 rounded-lg p-4 dark:bg-neutral-700">
                                    <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-3">
                                        Documentos
                                    </h4>
                                    <div class="space-y-3">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Documento Original:</span>
                                            <div class="flex items-center gap-3">
                                                <a href="{{ route('documents.requests.preview', $request->id) }}"
                                                   target="_blank"
                                                   class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 dark:bg-neutral-800 dark:border-neutral-700 dark:text-white dark:hover:bg-neutral-700">
                                                    <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                                                        <circle cx="12" cy="12" r="3"/>
                                                    </svg>
                                                    Ver documento
                                                </a>
                                                <a href="{{ route('documents.requests.download', $request->id) }}"
                                                   class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 dark:bg-neutral-800 dark:border-neutral-700 dark:text-white dark:hover:bg-neutral-700">
                                                    <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                                        <polyline points="7 10 12 15 17 10"/>
                                                        <line x1="12" x2="12" y1="15" y2="3"/>
                                                    </svg>
                                                    Descargar
                                                </a>
                                            </div>
                                        </div>

                                        @if($request->final_document_path)
                                        <div class="flex flex-col mt-4">
                                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Documento Final:</span>
                                            <div class="flex items-center gap-3">
                                                <a href="{{ route('documents.requests.preview-final', $request->id) }}"
                                                   target="_blank"
                                                   class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 dark:bg-neutral-800 dark:border-neutral-700 dark:text-white dark:hover:bg-neutral-700">
                                                    <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                                                        <circle cx="12" cy="12" r="3"/>
                                                    </svg>
                                                    Ver documento
                                                </a>
                                                <a href="{{ route('documents.requests.download-final', $request->id) }}"
                                                   class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 dark:bg-neutral-800 dark:border-neutral-700 dark:text-white dark:hover:bg-neutral-700">
                                                    <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                                        <polyline points="7 10 12 15 17 10"/>
                                                        <line x1="12" x2="12" y1="15" y2="3"/>
                                                    </svg>
                                                    Descargar
                                                </a>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Detalles y Observaciones -->
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
                            </div>
                        </div>
                    </div>

                    <!-- Footer del Modal -->
                    <div class="flex justify-end items-center gap-x-2 py-3 px-4 border-t dark:border-neutral-700">
                        <button type="button"
                        data-hs-overlay="#approve-modal-{{ $request->id }}"
                        class="w-full sm:w-auto py-2.5 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-green-600 text-white hover:bg-green-700 disabled:opacity-50 disabled:pointer-events-none">
                            <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 6 9 17l-5-5"/>
                            </svg>
                            Aprobar
                        </button>

                        <button type="button"
                                data-hs-overlay="#return-modal-{{ $request->id }}"
                                class="w-full sm:w-auto py-2.5 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-yellow-500 text-white hover:bg-yellow-600 disabled:opacity-50 disabled:pointer-events-none">
                            <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 14 4 9l5-5"/>
                                <path d="M4 9h10.5a5.5 5.5 0 0 1 5.5 5.5v0a5.5 5.5 0 0 1-5.5 5.5H11"/>
                            </svg>
                            Devolver
                        </button>

                        <button type="button"
                                data-hs-overlay="#request-modal-{{ $request->id }}"
                                class="w-full sm:w-auto py-2.5 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-lg border-2 border-gray-200 bg-white text-gray-700 hover:bg-gray-50 hover:text-gray-900">
                            <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M18 6 6 18"/><path d="m6 6 12 12"/>
                            </svg>
                            Cerrar
                        </button>
                        </div>
                    </div>
                    
                </div>
            </div>
            <!-- Modal de Aprobación -->
            <div id="approve-modal-{{ $request->id }}" class="hs-overlay hidden w-full h-full fixed top-0 start-0 z-[70] overflow-x-hidden overflow-y-auto">
                <div class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all sm:max-w-lg sm:w-full m-3 sm:mx-auto min-h-[calc(100%-3.5rem)] flex items-center">
                    <div class="relative flex flex-col bg-white shadow-lg rounded-xl dark:bg-neutral-800">
                        <div class="p-4 sm:p-10 text-center overflow-y-auto">
                            <span class="mb-4 inline-flex justify-center items-center w-[46px] h-[46px] rounded-full border-4 border-green-50 bg-green-100 text-green-500 dark:bg-green-700 dark:border-green-600 dark:text-green-100">
                                <svg class="size-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 6 9 17l-5-5"/>
                                </svg>
                            </span>

                            <h3 class="mb-2 text-xl font-bold text-gray-800 dark:text-gray-200">
                                Aprobar Documento
                            </h3>
                            <p class="text-gray-500 dark:text-neutral-400">
                                ¿Está seguro que desea aprobar este documento? Esta acción cambiará el estado a "Publicado".
                            </p>

                            <div class="mt-6">
                                <form action="{{ route('documents.requests.approve', $request->id) }}" method="POST">
                                    @csrf
                                    
                                    <div class="mt-6 flex justify-center gap-x-4">
                                        <button type="button"
                                                class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-slate-900 dark:border-gray-700 dark:text-white dark:hover:bg-gray-800 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600"
                                                data-hs-overlay="#approve-modal-{{ $request->id }}">
                                            Cancelar
                                        </button>
                                        <button type="submit"
                                                class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-green-600 text-white hover:bg-green-700 disabled:opacity-50 disabled:pointer-events-none dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600">
                                            Aprobar Documento
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal de Devolución -->
            <div id="return-modal-{{ $request->id }}" class="hs-overlay hidden w-full h-full fixed top-0 start-0 z-[70] overflow-x-hidden overflow-y-auto">
                <div class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all sm:max-w-lg sm:w-full m-3 sm:mx-auto min-h-[calc(100%-3.5rem)] flex items-center">
                    <div class="relative flex flex-col bg-white shadow-lg rounded-xl dark:bg-neutral-800">
                        <div class="p-4 sm:p-10 text-center overflow-y-auto">
                            <span class="mb-4 inline-flex justify-center items-center w-[46px] h-[46px] rounded-full border-4 border-yellow-50 bg-yellow-100 text-yellow-500 dark:bg-yellow-700 dark:border-yellow-600 dark:text-yellow-100">
                                <svg class="size-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M9 14 4 9l5-5"/>
                                    <path d="M4 9h10.5a5.5 5.5 0 0 1 5.5 5.5v0a5.5 5.5 0 0 1-5.5 5.5H11"/>
                                </svg>
                            </span>

                            <h3 class="mb-2 text-xl font-bold text-gray-800 dark:text-gray-200">
                                Devolver a Elaboración
                            </h3>
                            <p class="text-gray-500 dark:text-neutral-400">
                                ¿Está seguro que desea devolver este documento a elaboración? Esta acción cambiará el estado a "En Elaboración".
                            </p>

                            <div class="mt-6">
                                <form action="{{ route('documents.requests.return-to-progress', $request->id) }}" method="POST">
                                    @csrf
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium mb-2 dark:text-white">Observaciones <span class="text-red-500">*</span></label>
                                        <textarea name="observations" 
                                                class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-slate-900 dark:border-gray-700 dark:text-gray-400 dark:focus:ring-gray-600"
                                                rows="3"
                                                required></textarea>
                                    </div>
                                    
                                    <div class="mt-6 flex justify-center gap-x-4">
                                        <button type="button"
                                                class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-slate-900 dark:border-gray-700 dark:text-white dark:hover:bg-gray-800 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600"
                                                data-hs-overlay="#return-modal-{{ $request->id }}">
                                            Cancelar
                                        </button>
                                        <button type="submit"
                                                class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-yellow-500 text-white hover:bg-yellow-600 disabled:opacity-50 disabled:pointer-events-none dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600">
                                            Devolver a Elaboración
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endsection
    
    <!-- Scripts adicionales para la sección -->
    @push('scripts')
    <script>
        // Script para la búsqueda en la tabla
        document.getElementById('hs-table-search').addEventListener('input', function(e) {
            const searchText = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchText) ? '' : 'none';
            });
        });
    
        // Script para ocultar las alertas automáticamente después de 5 segundos
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                const successAlert = document.getElementById('success-alert');
                const errorAlert = document.getElementById('error-alert');
                
                if (successAlert) {
                    successAlert.style.display = 'none';
                }
                if (errorAlert) {
                    errorAlert.style.display = 'none';
                }
            }, 5000);
        });
    </script>
    @endpush