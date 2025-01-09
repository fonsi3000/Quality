@extends('layouts.app')

@section('title', 'Documentos Vigentes')

@section('content')
<!-- Sección de Búsqueda y Filtros -->
<div class="space-y-4">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
            Documentos Vigentes
        </h1>
        
    </div>
    <div class="bg-white dark:bg-neutral-900 border border-gray-200 dark:border-neutral-700 rounded-xl p-4">
        <form action="{{ route('documents.published') }}" method="GET">
            @csrf 
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
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
    
                <!-- Filtro por Proceso -->
                <div class="relative">
                    <label for="process_search" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                        Proceso
                    </label>
                    <div class="relative">
                        <input
                            type="text"
                            id="process_search"
                            class="py-2 px-3 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-800 dark:border-neutral-700 dark:text-gray-400"
                            placeholder="Buscar proceso..."
                            autocomplete="off"
                        >
                        <input type="hidden" name="process_id" id="process_id" value="{{ request('process_id') }}">
                        
                        <!-- Dropdown de autocompletado -->
                        <div id="process_dropdown" 
                             class="hidden absolute left-0 right-0 z-50 w-full bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-lg shadow-lg max-h-60 overflow-y-auto mt-1">
                            <!-- Las opciones se agregarán dinámicamente aquí -->
                        </div>
                    </div>
                </div>
    
                <!-- Filtro por Público/Privado -->
                <div>
                    <label for="is_public" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                        Visibilidad
                    </label>
                    <select name="is_public" 
                            id="is_public" 
                            class="py-2 px-3 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-800 dark:border-neutral-700 dark:text-gray-400">
                        <option value="all">Todos</option>
                        <option value="1" {{ request('is_public') == '1' ? 'selected' : '' }}>Público</option>
                        <option value="0" {{ request('is_public') == '0' ? 'selected' : '' }}>Privado</option>
                    </select>
                </div>
            </div>
    
            <!-- Botones de Acción -->
            <div class="mt-4 flex justify-end gap-x-2">
                <a href="{{ route('documents.published') }}" 
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
                                            Origen
                                        </span>
                                    </div>
                                </th>
                                <th scope="col" class="px-4 py-3 text-start">
                                    <div class="flex items-center gap-x-2">
                                        <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 dark:text-neutral-200">
                                            Responsable
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
                                            Fecha Publicación
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
                                                {{ $request->document_name }}
                                            </span>
                                            {{-- <span class="text-xs text-gray-500 dark:text-neutral-400">
                                                {{ $request->documentType->name }}
                                            </span> --}}
                                        </div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="text-sm text-gray-800 dark:text-neutral-200">
                                            {{ $request->origin }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="text-sm text-gray-800 dark:text-neutral-200">
                                            {{ $request->responsible->name }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="text-sm text-gray-800 dark:text-neutral-200">
                                            v{{ $request->version }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="text-sm text-gray-800 dark:text-neutral-200">
                                            {{ $request->updated_at->format('d/m/Y H:i') }}
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

                                    <!-- Responsable -->
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Responsable:</span>
                                        <span class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                                            {{ $request->responsible->name ?? 'No asignado' }}
                                        </span>
                                    </div>

                                    <!-- Agente Asignado -->
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Agente Asignado:</span>
                                        <span class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                                            {{ $request->assignedAgent?->name ?? 'No asignado' }}
                                        </span>
                                    </div>
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

                                @can('admin.agent')
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
                                @can('admin.agent')
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

                            <!-- Acciones Disponibles -->
                            
                            <div class="bg-gray-50 rounded-xl p-6 dark:bg-neutral-700 border border-gray-100 dark:border-neutral-600">
                                <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center gap-2">
                                    <svg class="size-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                    Acciones Disponibles
                                </h4>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <a href="{{ route('documents.requests.edit', $request->id) }}"
                                    class="inline-flex justify-center items-center gap-2 px-4 py-2 text-sm font-medium text-blue-700 bg-blue-100 border border-transparent rounded-lg hover:bg-blue-200 transition-colors">
                                        <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Editar Documento
                                    </a>

                                    <button type="button"
                                            onclick="if(confirm('¿Estás seguro de que deseas eliminar este documento?')) document.getElementById('delete-form-{{ $request->id }}').submit();"
                                            class="inline-flex justify-center items-center gap-2 px-4 py-2 text-sm font-medium text-red-700 bg-red-100 border border-transparent rounded-lg hover:bg-red-200 transition-colors">
                                        <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        Eliminar Documento
                                    </button>

                                    <form id="delete-form-{{ $request->id }}" 
                                        action="{{ route('documents.requests.destroy', $request->id) }}" 
                                        method="POST" 
                                        style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </div>
                            </div>
                        @endcan
                        @if(Auth::user()->can('admin.agent') || (Auth::user()->process && Auth::user()->process->leader_id == Auth::id()))
                        <!-- Estado de Visibilidad -->

                        <div class="bg-gray-50 rounded-xl p-6 dark:bg-neutral-700 border border-gray-100 dark:border-neutral-600 mt-4">
                            <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center gap-2">
                                <svg class="size-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                Estado de Visibilidad
                            </h4>
                            <div class="space-y-4">
                                <!-- Estado actual -->
                                <div class="flex items-center justify-between p-4 bg-white dark:bg-neutral-800 rounded-lg border border-gray-200 dark:border-neutral-600">
                                    <div class="flex items-center gap-3">
                                        <div class="flex-shrink-0">
                                            @if($request->is_public)
                                                <span class="inline-flex items-center justify-center size-8 rounded-full bg-green-100 dark:bg-green-900/50">
                                                    <svg class="size-4 text-green-600 dark:text-green-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                    </svg>
                                                </span>
                                            @else
                                                <span class="inline-flex items-center justify-center size-8 rounded-full bg-yellow-100 dark:bg-yellow-900/50">
                                                    <svg class="size-4 text-yellow-600 dark:text-yellow-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                                    </svg>
                                                </span>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                Estado actual: {{ $request->is_public ? 'Público' : 'Privado' }}
                                            </p>
                                            <p class="text-sm text-gray-500 dark:text-neutral-400">
                                                {{ $request->is_public ? 'Este documento es visible para todos los usuarios.' : 'Este documento solo es visible para usuarios de tu proceso.' }}
                                            </p>
                                        </div>
                                    </div>
                                    <form action="{{ route('documents.requests.toggle-visibility', $request->id) }}" method="POST" class="flex-shrink-0">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg border transition-colors
                                                {{ $request->is_public 
                                                    ? 'text-yellow-700 bg-yellow-100 hover:bg-yellow-200 border-yellow-200 dark:bg-yellow-900/50 dark:border-yellow-800 dark:text-yellow-400'
                                                    : 'text-green-700 bg-green-100 hover:bg-green-200 border-green-200 dark:bg-green-900/50 dark:border-green-800 dark:text-green-400' }}">
                                            <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                @if($request->is_public)
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                                @else
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                @endif
                                            </svg>
                                            {{ $request->is_public ? 'Hacer Privado' : 'Hacer Público' }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endif
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
        // Referencias a elementos del formulario
        const form = document.getElementById('filter-form');
        const searchInput = document.getElementById('search');
        const typeFilter = document.getElementById('document_type_id');
        const processSearch = document.getElementById('process_search');
        const processId = document.getElementById('process_id');
        const isPublicFilter = document.getElementById('is_public');
        const dropdown = document.getElementById('process_dropdown');
        
        // Inicialización del autocompletado de procesos
        const processes = @json($processes);
        
        // Establecer el valor inicial del proceso si existe
        if (processId.value) {
            const selectedProcess = processes.find(p => p.id == processId.value);
            if (selectedProcess) {
                processSearch.value = selectedProcess.name;
            }
        }

        // Funciones para el autocompletado
        function filterProcesses(searchTerm) {
            return processes.filter(process => 
                process.name.toLowerCase().includes(searchTerm.toLowerCase())
            );
        }

        function updateDropdown(filteredProcesses) {
            dropdown.innerHTML = '';
            
            // Agregar opción "Todos los procesos"
            const allOption = document.createElement('div');
            allOption.className = 'px-4 py-2 hover:bg-gray-100 dark:hover:bg-neutral-700 cursor-pointer';
            allOption.textContent = 'Todos los procesos';
            allOption.onclick = () => {
                processSearch.value = '';
                processId.value = 'all';
                dropdown.classList.add('hidden');
                // Disparar evento para actualizar filtros
                processId.dispatchEvent(new Event('change'));
            };
            dropdown.appendChild(allOption);

            // Agregar procesos filtrados
            filteredProcesses.forEach(process => {
                const div = document.createElement('div');
                div.className = 'px-4 py-2 hover:bg-gray-100 dark:hover:bg-neutral-700 cursor-pointer';
                div.textContent = process.name;
                div.onclick = () => {
                    processSearch.value = process.name;
                    processId.value = process.id;
                    dropdown.classList.add('hidden');
                    // Disparar evento para actualizar filtros
                    processId.dispatchEvent(new Event('change'));
                };
                dropdown.appendChild(div);
            });
        }

        // Función para actualizar la URL y hacer la petición
        function updateURL(params) {
            const url = new URL(window.location.href);
            
            // Limpiar parámetros existentes
            url.searchParams.forEach((value, key) => {
                if (!params.has(key)) {
                    url.searchParams.delete(key);
                }
            });
            
            // Agregar nuevos parámetros
            params.forEach((value, key) => {
                if (value) {
                    url.searchParams.set(key, value);
                } else {
                    url.searchParams.delete(key);
                }
            });
            
            // Actualizar URL sin recargar la página
            window.history.pushState({}, '', url);
            
            // Hacer la petición
            fetch(url.toString(), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(html => {
                // Actualizar solo la tabla de resultados
                document.querySelector('.table-container').innerHTML = html;
            })
            .catch(error => console.error('Error:', error));
        }

        // Función para aplicar los filtros
        function applyFilters() {
            const formData = new FormData(form);
            updateURL(formData);
        }

        // Event listeners para el autocompletado de procesos
        processSearch.addEventListener('input', function() {
            const filtered = filterProcesses(this.value);
            updateDropdown(filtered);
            dropdown.classList.remove('hidden');
        });

        processSearch.addEventListener('focus', function() {
            const filtered = filterProcesses(this.value);
            updateDropdown(filtered);
            dropdown.classList.remove('hidden');
        });

        // Cerrar dropdown cuando se hace clic fuera
        document.addEventListener('click', function(e) {
            if (!processSearch.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.classList.add('hidden');
            }
        });

        // Prevenir que el formulario se envíe al presionar enter en el campo de búsqueda de proceso
        processSearch.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
            }
        });

        // Event listeners para los filtros
        typeFilter.addEventListener('change', applyFilters);
        processId.addEventListener('change', applyFilters);
        isPublicFilter.addEventListener('change', applyFilters);

        // Debounce para el campo de búsqueda general
        let searchTimeout = null;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(applyFilters, 500);
        });

        // Prevenir el submit por defecto del formulario
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            applyFilters();
        });
    });
</script>
@endpush