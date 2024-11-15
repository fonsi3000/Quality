@extends('layouts.app')

@section('title', 'Documentos Publicados')

@section('content')
<!-- Sección de Búsqueda y Filtros -->
<div class="space-y-4">
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
                <div class="relative flex flex-col bg-white border shadow-sm rounded-xl w-full max-w-4xl dark:bg-neutral-800 dark:border-neutral-700">
                    <!-- Header del Modal -->
                    <div class="py-3 px-4 border-b dark:border-neutral-700">
                        <div class="flex justify-between items-center">
                            <h3 class="font-bold text-gray-800 dark:text-white">
                                {{ $request->document_name }} (v{{ $request->version }})
                            </h3>
                            <button type="button" 
                                    class="inline-flex items-center justify-center size-8 text-sm font-semibold rounded-lg border border-transparent text-gray-800 hover:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:text-white dark:hover:bg-gray-700 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600"
                                    data-hs-overlay="#request-modal-{{ $request->id }}">
                                <span class="sr-only">Cerrar</span>
                                <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M18 6 6 18"/><path d="m6 6 12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Body del Modal -->
                    <div class="p-4 overflow-y-auto">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            <!-- Columna Izquierda -->
                            <div class="space-y-4">
                                <!-- Información del Documento -->
                                <div class="bg-gray-50 rounded-lg p-4 dark:bg-neutral-700">
                                    <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-3">
                                        Información del Documento
                                    </h4>
                                    <div class="space-y-3">
                                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center">
                                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Tipo:</span>
                                            <span class="text-sm text-gray-800 dark:text-gray-200 mt-1 sm:mt-0">
                                                {{ $request->documentType->name }}
                                            </span>
                                        </div>
                                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center">
                                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Versión:</span>
                                            <span class="text-sm text-gray-800 dark:text-gray-200 mt-1 sm:mt-0">
                                                v{{ $request->version }}
                                            </span>
                                        </div>
                                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center">
                                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Origen:</span>
                                            <span class="text-sm text-gray-800 dark:text-gray-200 mt-1 sm:mt-0">
                                                {{ $request->origin }}
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
                                                {{ $request->assignedAgent?->name ?? 'No asignado' }}
                                            </span>
                                        </div>
                                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center">
                                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Fecha de Creación:</span>
                                            <span class="text-sm text-gray-800 dark:text-gray-200 mt-1 sm:mt-0">
                                                {{ $request->created_at->format('d/m/Y H:i') }}
                                            </span>
                                        </div>
                                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center">
                                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Última Actualización:</span>
                                            <span class="text-sm text-gray-800 dark:text-gray-200 mt-1 sm:mt-0">
                                                {{ $request->updated_at->format('d/m/Y H:i') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Descripción -->
                                <div class="bg-gray-50 rounded-lg p-4 dark:bg-neutral-700">
                                    <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-3">
                                        Descripción del documento
                                    </h4>
                                    <p class="text-sm text-gray-800 dark:text-gray-200">
                                        {{ $request->description ?: 'Sin descripción disponible' }}
                                    </p>
                                </div>

                                <!-- Observaciones -->
                                <div class="bg-gray-50 rounded-lg p-4 dark:bg-neutral-700">
                                    <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-3">
                                        Observacion de calidad
                                    </h4>
                                    <p class="text-sm text-gray-800 dark:text-gray-200">
                                        {{ $request->observations ?: 'Sin observaciones' }}
                                    </p>
                                </div>
                            </div>

                            <!-- Columna Derecha -->
                            <div class="space-y-4">
                                <!-- Historial de versiones -->
                                <div class="bg-gray-50 rounded-lg p-4 dark:bg-neutral-700">
                                    <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-3">
                                        Documento
                                    </h4>
                                    @can('admin.agent')
                                    <!-- Documento Original -->
                                    <div class="mb-4">
                                        <h5 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Documento Borrador
                                        </h5>
                                        <div class="flex items-center gap-3">
                                            <a href="{{ route('documents.requests.preview-document', $request->id) }}"
                                               target="_blank"
                                               class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 dark:bg-neutral-800 dark:border-neutral-700 dark:text-white dark:hover:bg-neutral-700">
                                                <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                                                    <circle cx="12" cy="12" r="3"/>
                                                </svg>
                                                Ver documento
                                            </a>
                                            <a href="{{ route('documents.requests.download-document', $request->id) }}"
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
                                    @endcan
                                    <!-- Documento Final -->
                                    @if($request->final_document_path)
                                    <div>
                                        <h5 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Documento Final (Publicado)
                                        </h5>
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
                                                Documento
                                            </a>
                                        </div>
                                    </div>
                                    @endif
                                </div>

                                <!-- Historial de Estado -->
                                <div class="bg-gray-50 rounded-lg p-4 dark:bg-neutral-700">
                                    <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-3">
                                        Estado Actual
                                    </h4>
                                    <div class="mb-4">
                                        <span class="inline-flex items-center gap-1.5 py-1.5 px-3 rounded-full text-sm font-medium {{ $statusClasses[$request->status] }}">
                                            {{ $statusLabels[$request->status] }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer del Modal -->
                    <div class="flex justify-end items-center gap-x-2 py-3 px-4 border-t dark:border-neutral-700">
                        <button type="button"
                                data-hs-overlay="#request-modal-{{ $request->id }}"
                                class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 dark:bg-neutral-800 dark:border-neutral-700 dark:text-white dark:hover:bg-neutral-700">
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

        // Event listeners
        typeFilter.addEventListener('change', applyFilters);
        dateFromInput.addEventListener('change', applyFilters);
        dateToInput.addEventListener('change', applyFilters);

        // Debounce para la búsqueda por texto
        let timeout = null;
        searchInput.addEventListener('input', function() {
            clearTimeout(timeout);
            timeout = setTimeout(applyFilters, 500);
        });

        // Prevenir el submit por defecto del formulario
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            applyFilters();
        });
    });
</script>
@endpush