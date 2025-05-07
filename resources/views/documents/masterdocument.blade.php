@extends('layouts.app')

@section('title', 'Listado Maestro de Documentos')

@section('content')
<div class="space-y-4">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
            Listado Maestro de Documentos
        </h1>
        <p class="text-sm text-gray-600 dark:text-gray-400">
            Gestión y seguimiento de documentos obsoletos
        </p>
    </div>
    <!-- Sección de Búsqueda y Filtros -->
    <div class="bg-white dark:bg-neutral-900 border border-gray-200 dark:border-neutral-700 rounded-xl p-4">
        <form id="filter-form" action="{{ route('documents.masterdocument') }}" method="GET">
            @csrf 
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-5">
                <!-- Búsqueda por texto -->
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                        Buscar
                    </label>
                    <input type="text" 
                           name="search" 
                           id="search"
                           value="{{ request('search') }}"
                           class="py-2 px-3 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-800 dark:border-neutral-700 dark:text-gray-400"
                           placeholder="Buscar por nombre, tipo...">
                </div>

                <!-- Filtro por Tipo de Documento -->
                <div>
                    <label for="document_type_id" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                        Tipo de Documento
                    </label>
                    <select name="document_type_id" 
                            id="document_type_id" 
                            class="py-2 px-3 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-800 dark:border-neutral-700 dark:text-gray-400">
                        <option value="all">Todos los tipos</option>
                        @foreach($documentTypes as $type)
                            <option value="{{ $type->id }}" {{ request('document_type_id') == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="process_id" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                        Proceso
                    </label>
                    <select name="process_id" 
                            id="process_id" 
                            class="py-2 px-3 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-800 dark:border-neutral-700 dark:text-gray-400">
                        <option value="all">Todos los procesos</option>
                        @foreach($processes as $process)
                            <option value="{{ $process->id }}" {{ request('process_id') == $process->id ? 'selected' : '' }}>
                                {{ $process->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Rango de Fechas -->
                <div>
                    <label for="date_from" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                        Desde
                    </label>
                    <input type="date" 
                           name="date_from" 
                           id="date_from"
                           value="{{ request('date_from') }}"
                           class="py-2 px-3 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-800 dark:border-neutral-700 dark:text-gray-400">
                </div>

                <div>
                    <label for="date_to" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                        Hasta
                    </label>
                    <input type="date" 
                           name="date_to" 
                           id="date_to"
                           value="{{ request('date_to') }}"
                           class="py-2 px-3 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-800 dark:border-neutral-700 dark:text-gray-400">
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="mt-4 flex justify-end gap-x-2">
                <a href="{{ route('documents.masterdocument') }}" 
                   class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 dark:bg-neutral-800 dark:border-neutral-700 dark:text-white dark:hover:bg-neutral-700">
                    Limpiar filtros
                </a>
                <button type="submit"
                        class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600">
                    <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"/>
                        <path d="m21 21-4.3-4.3"/>
                    </svg>
                    Buscar
                </button>
            </div>
        </form>
    </div>

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
                                            Nombre del Documento
                                        </span>
                                    </div>
                                </th>
                                <th scope="col" class="px-4 py-3 text-start">
                                    <div class="flex items-center gap-x-2">
                                        <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 dark:text-neutral-200">
                                            Versión
                                        </span>
                                    </div>
                                </th>
                                <th scope="col" class="px-4 py-3 text-start">
                                    <div class="flex items-center gap-x-2">
                                        <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 dark:text-neutral-200">
                                            Fecha de actualizacion
                                        </span>
                                    </div>
                                </th>
                                <th scope="col" class="px-4 py-3 text-start">
                                    <div class="flex items-center gap-x-2">
                                        <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 dark:text-neutral-200">
                                            Proceso
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
                                    {{-- <td class="px-4 py-4">
                                        <span class="text-sm text-gray-800 dark:text-neutral-200">
                                            {{ $request->document_code }}
                                        </span>
                                    </td> --}}
                                    <td class="px-4 py-4">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-medium text-gray-800 dark:text-neutral-200">
                                                {{ $request->document_name }}
                                            </span>
                                            <span class="text-xs text-gray-500 dark:text-neutral-400">
                                                {{ $request->documentType->name }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="text-sm text-gray-800 dark:text-neutral-200">
                                            v{{ $request->version }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="text-sm text-gray-800 dark:text-neutral-200">
                                            {{ $request->updated_at->format('d/m/Y') }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="text-sm text-gray-800 dark:text-neutral-200">
                                            {{ $request->process->name }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="inline-flex items-center gap-1.5 py-1.5 px-3 rounded-full text-sm font-medium {{ $statusClasses[$request->status] }}">
                                            {{ $statusLabels[$request->status] }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-end sticky right-0 bg-white dark:bg-neutral-900">
                                        <div class="flex items-center justify-end gap-2">
                                            <button type="button" 
                                                    data-hs-overlay="#request-modal-{{ $request->id }}"
                                                    class="text-blue-600 hover:text-blue-400 dark:text-blue-500 dark:hover:text-blue-400">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5">
                                                    <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                                                    <circle cx="12" cy="12" r="3"/>
                                                </svg>
                                            </button>
                                            @if($request->final_document_path)
                                            <a href="{{ route('documents.requests.download-final', $request->id) }}"
                                               class="text-blue-600 hover:text-blue-400 dark:text-blue-500 dark:hover:text-blue-400">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5">
                                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                                    <polyline points="7 10 12 15 17 10"/>
                                                    <line x1="12" x2="12" y1="15" y2="3"/>
                                                </svg>
                                            </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Paginación -->
    <div class="mt-4">
        {{ $documentRequests->appends(request()->query())->links() }}
    </div>

    <!-- Modales -->
    @foreach($documentRequests as $request)
    <div id="request-modal-{{ $request->id }}" class="hs-overlay hidden w-full h-full fixed top-0 start-0 z-[60] overflow-x-hidden overflow-y-auto">
        <div class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all w-[95%] sm:w-[90%] md:w-[85%] m-3 mx-auto min-h-[calc(100%-3.5rem)] flex items-center justify-center">
            <div class="relative flex flex-col bg-white border shadow-sm rounded-xl w-full max-w-5xl dark:bg-neutral-800 dark:border-neutral-700">
                <!-- Header Mejorado -->
                <div class="py-4 px-4 border-b dark:border-neutral-700 bg-gray-50 dark:bg-neutral-700 rounded-t-xl">
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
                                class="inline-flex items-center justify-center size-8 text-sm font-semibold rounded-lg border border-transparent text-gray-800 hover:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:text-white dark:hover:bg-gray-700 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600"
                                data-hs-overlay="#request-modal-{{ $request->id }}">
                            <span class="sr-only">Cerrar</span>
                            <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Body del Modal -->
                <div class="p-6 overflow-y-auto">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Columna Izquierda -->
                        <div class="space-y-6">
                            <!-- Información General -->
                            <div class="bg-gray-50 rounded-xl p-6 dark:bg-neutral-700 border border-gray-100 dark:border-neutral-600">
                                <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center gap-2">
                                    <svg class="size-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Información del Documento
                                </h4>
                                <div class="space-y-4">
                                    <!-- Tipo de Solicitud -->
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Tipo de Solicitud:</span>
                                        <span class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                                            {{ $request->getRequestTypeLabel() }}
                                        </span>
                                    </div>
                                    
                                    <!-- Proceso -->
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Proceso:</span>
                                        <span class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                                            {{ $request->process->name ?? 'No asignado' }}
                                        </span>
                                    </div>

                                    <!-- Tipo de Documento -->
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Tipo de Documento:</span>
                                        <span class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                                            {{ $request->documentType->name }}
                                        </span>
                                    </div>

                                    <!-- Origen y Destino -->
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Origen:</span>
                                        <span class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                                            {{ $request->origin }}
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Destino:</span>
                                        <span class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                                            {{ $request->destination }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Responsables -->
                            <div class="bg-gray-50 rounded-xl p-6 dark:bg-neutral-700 border border-gray-100 dark:border-neutral-600">
                                <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center gap-2">
                                    <svg class="size-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                    </svg>
                                    Responsables
                                </h4>
                                <div class="space-y-4">
                                    <!-- Solicitante -->
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Solicitante:</span>
                                        <span class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                                            {{ $request->user->name }}
                                        </span>
                                    </div>

                                    <!-- Elaborado por -->
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Elaborado por:</span>
                                        <span class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                                            {{ $request->assignedAgent?->name ?? 'No asignado' }}
                                        </span>
                                    </div>

                                    <!-- Revisado por -->
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Revisado por:</span>
                                        <span class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                                            {{ $request->responsible?->name ?? 'No asignado' }}
                                        </span>
                                    </div>

                                    <!-- Aprobado por líder principal -->
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Aprobado por líder principal:</span>
                                        <span class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                                            {{ $request->process?->leader?->name ?? 'No asignado' }}
                                        </span>
                                    </div>

                                    <!-- Aprobado por segundo líder -->
                                    @if ($request->process?->second_leader_id)
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Aprobado por segundo líder:</span>
                                            <span class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                                                {{ $request->process?->secondLeader?->name ?? 'No asignado' }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Fechas -->
                            <div class="bg-gray-50 rounded-xl p-6 dark:bg-neutral-700 border border-gray-100 dark:border-neutral-600">
                                <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center gap-2">
                                    <svg class="size-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    Fechas Importantes
                                </h4>
                                <div class="space-y-4">
                                    <!-- Fecha de Creación -->
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Creación:</span>
                                        <span class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                                            {{ $request->created_at->format('d/m/Y H:i') }}
                                        </span>
                                    </div>

                                    <!-- Última Actualización -->
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Última Actualización:</span>
                                        <span class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                                            {{ $request->updated_at->format('d/m/Y H:i') }}
                                        </span>
                                    </div>

                                    <!-- Fecha de Aprobación del Líder -->
                                    @if($request->leader_approval_date)
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Aprobación del Líder:</span>
                                        <span class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                                            {{ $request->leader_approval_date->format('d/m/Y H:i') }}
                                        </span>
                                    </div>
                                    @endif
                                    @if($request->second_leader_approval_date)
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Aprobación del Segundo Líder:</span>
                                        <span class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                                            {{ $request->second_leader_approval_date->format('d/m/Y H:i') }}
                                        </span>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Columna Derecha -->
                        <div class="space-y-6">
                            <!-- Estado Actual -->
                            <div class="bg-gray-50 rounded-xl p-6 dark:bg-neutral-700 border border-gray-100 dark:border-neutral-600">
                                <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center gap-2">
                                    <svg class="size-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Estado del Documento
                                </h4>
                                <div class="space-y-4">
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex items-center gap-1.5 py-1.5 px-3 rounded-full text-sm font-semibold 
                                            {{ match($request->status) {
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
                                </div>
                            </div>

                            <!-- Descripción y Observaciones -->
                            <div class="bg-gray-50 rounded-xl p-6 dark:bg-neutral-700 border border-gray-100 dark:border-neutral-600">
                                <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Descripción</h4>
                                <p class="text-sm text-gray-700 dark:text-gray-300 mb-6">
                                    {{ $request->description ?: 'Sin descripción disponible' }}
                                </p>

                                @if($request->observations)
                                <div class="mt-4">
                                    <h5 class="text-md font-semibold text-gray-800 dark:text-gray-200 mb-2">Observaciones de Calidad</h5>
                                    <p class="text-sm text-gray-700 dark:text-gray-300">
                                        {{ $request->observations }}
                                    </p>
                                </div>
                                @endif

                                @if($request->leader_observations)
                                <div class="mt-4">
                                    <h5 class="text-md font-semibold text-gray-800 dark:text-gray-200 mb-2">
                                        Observaciones del Líder
                                    </h5>
                                    <p class="text-sm text-gray-700 dark:text-gray-300">
                                        {{ $request->leader_observations }}
                                    </p>
                                </div>
                                @endif
                            </div>

                            <!-- Documentos -->
                            <div class="bg-gray-50 rounded-xl p-6 dark:bg-neutral-700 border border-gray-100 dark:border-neutral-600">
                                <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center gap-2">
                                    <svg class="size-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    Documentos Asociados
                                </h4>

                                @can('admin.only')
                                <!-- Documento Borrador -->
                                <div class="mb-6">
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
                                <div class="mt-6">
                                    <h5 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                        Historial de Versiones
                                    </h5>
                                    <div class="space-y-3">
                                        @if($request->hasReferenceDocument())
                                        <div class="p-4 rounded-lg bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700">
                                            <div class="flex items-center justify-between">
                                                <div class="flex flex-col">
                                                    <span class="text-sm font-medium text-gray-800 dark:text-gray-200">
                                                        Versión Original
                                                    </span>
                                                    <span class="text-xs text-gray-500">
                                                        v{{ $request->referenceDocument->version }}
                                                    </span>
                                                </div>
                                                <a href="{{ route('documents.requests.preview', $request->referenceDocument->id) }}"
                                                   target="_blank"
                                                   class="text-blue-600 hover:text-blue-700 dark:text-blue-500 dark:hover:text-blue-400">
                                                    <svg class="size-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                    </svg>
                                                </a>
                                            </div>
                                        </div>
                                        @endif

                                        @foreach($request->getVersionHistory() as $version)
                                        <div class="p-4 rounded-lg bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700">
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
                <div class="flex justify-end items-center gap-x-2 py-4 px-6 border-t dark:border-neutral-700 bg-gray-50 dark:bg-neutral-700 rounded-b-xl">
                    <button type="button"
                            data-hs-overlay="#request-modal-{{ $request->id }}"
                            class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg shadow-sm hover:bg-gray-50 dark:bg-neutral-800 dark:border-neutral-700 dark:text-gray-300 dark:hover:bg-neutral-700 transition-colors">
                        <svg class="size-4 me-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>
@endforeach
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('filter-form');
        const searchInput = document.getElementById('search');
        const typeFilter = document.getElementById('document_type_id');
        const dateFromInput = document.getElementById('date_from');
        const dateToInput = document.getElementById('date_to');
        
        // Guardar el valor original del campo de búsqueda
        const originalSearchValue = searchInput ? searchInput.value : '';
        let isTyping = false;

        // Función simple para actualizar la página con los filtros
        function updateFilters(fromSearch = false) {
            // Si es desde el campo de búsqueda y el usuario aún está escribiendo, no hacer nada
            if (fromSearch && isTyping) {
                return;
            }
            
            // Construir la URL base
            let url = new URL(window.location.href);
            
            // Actualizar parámetros
            let formData = new FormData(form);
            formData.forEach((value, key) => {
                if (value) {
                    url.searchParams.set(key, value);
                } else {
                    url.searchParams.delete(key);
                }
            });
            
            // Si viene desde el campo de búsqueda, guardar el estado
            if (fromSearch && searchInput) {
                sessionStorage.setItem('searchValue', searchInput.value);
                sessionStorage.setItem('cursorPosition', searchInput.selectionStart || 0);
                sessionStorage.setItem('isSearching', 'true');
            }
            
            // Redireccionar con los nuevos parámetros
            window.location.href = url.toString();
        }

        // Event listeners para los filtros
        if (typeFilter) {
            typeFilter.addEventListener('change', function(e) {
                e.preventDefault();
                updateFilters(false);
            });
        }

        if (dateFromInput) {
            dateFromInput.addEventListener('change', function(e) {
                e.preventDefault();
                updateFilters(false);
            });
        }

        if (dateToInput) {
            dateToInput.addEventListener('change', function(e) {
                e.preventDefault();
                updateFilters(false);
            });
        }

        // Mejorar el debounce para la búsqueda
        let timeout = null;
        if (searchInput) {
            // Detectar cuando el usuario está escribiendo
            searchInput.addEventListener('focus', function() {
                isTyping = true;
            });
            
            searchInput.addEventListener('blur', function() {
                isTyping = false;
                
                // Si hay un cambio pendiente, aplicarlo
                if (timeout && searchInput.value !== originalSearchValue) {
                    clearTimeout(timeout);
                    updateFilters(true);
                }
            });
            
            searchInput.addEventListener('input', function(e) {
                clearTimeout(timeout);
                
                // Solo actualizar cuando el usuario haya dejado de escribir por un tiempo
                timeout = setTimeout(function() {
                    if (searchInput.value.length > 2 || searchInput.value.length === 0) {
                        updateFilters(true);
                    }
                }, 800); // Más tiempo para escribir
            });
            
            // Manejar la tecla Enter para buscar inmediatamente
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    clearTimeout(timeout);
                    updateFilters(true);
                }
            });
            
            // Restaurar el estado después de la carga si estábamos buscando
            if (sessionStorage.getItem('isSearching') === 'true') {
                const savedValue = sessionStorage.getItem('searchValue');
                const cursorPos = parseInt(sessionStorage.getItem('cursorPosition') || '0');
                
                if (savedValue) {
                    // Enfocar y seleccionar después de un pequeño retraso
                    setTimeout(() => {
                        searchInput.focus();
                        searchInput.setSelectionRange(cursorPos, cursorPos);
                    }, 100);
                }
                
                // Limpiar el estado
                sessionStorage.removeItem('isSearching');
                sessionStorage.removeItem('searchValue');
                sessionStorage.removeItem('cursorPosition');
            }
        }

        // Prevenir el submit del formulario y manejar la búsqueda
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                updateFilters(false);
            });
        }
    });
</script>
@endpush