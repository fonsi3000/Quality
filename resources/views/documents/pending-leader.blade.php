@extends('layouts.app')

@section('title', 'Aprobación de Documentos')

@section('content')
<div class="flex flex-col">
    <div class="overflow-x-auto min-h-[631px]">
        <div class="min-w-full inline-block align-middle">
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm dark:bg-neutral-800 dark:border-neutral-700">
                <!-- Header -->
                <div class="px-4 py-4 grid gap-3 md:flex md:justify-between md:items-center border-b border-gray-200 dark:border-neutral-700">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800 dark:text-neutral-200">
                            Documentos Pendientes de Aprobación
                        </h2>
                    </div>

                    <!-- Filtros -->
                    <div class="flex flex-col sm:flex-row gap-3">
                        <!-- Búsqueda -->
                        <div class="grow">
                            <label for="search" class="sr-only">Buscar</label>
                            <div class="relative">
                                <input type="text" 
                                       id="search" 
                                       class="py-2 px-3 ps-11 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-400" 
                                       placeholder="Buscar documentos">
                                <div class="absolute inset-y-0 start-0 flex items-center ps-4">
                                    <svg class="size-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Filtro Estado -->
                        <div class="w-full sm:w-auto">
                            <select id="status-filter" 
                                    class="py-2 px-3 pe-9 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-400">
                                <option value="">Todos los estados</option>
                                <option value="pendiente_lider">Pendiente de Líder Principal</option>
                                <option value="pendiente_segundo_lider">Pendiente de Segundo Líder</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Tabla -->
                <div class="overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                        <thead class="bg-gray-50 dark:bg-neutral-800">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-start">
                                    <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 dark:text-neutral-200">
                                        Documento
                                    </span>
                                </th>
                                <th scope="col" class="px-6 py-3 text-start">
                                    <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 dark:text-neutral-200">
                                        Solicitante
                                    </span>
                                </th>
                                <th scope="col" class="px-6 py-3 text-start">
                                    <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 dark:text-neutral-200">
                                        Fecha
                                    </span>
                                </th>
                                <th scope="col" class="px-6 py-3 text-start">
                                    <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 dark:text-neutral-200">
                                        Estado
                                    </span>
                                </th>
                                <th scope="col" class="px-6 py-3 text-end">
                                    <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 dark:text-neutral-200">
                                        Acciones
                                    </span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-neutral-700">
                            @foreach($documentRequests as $request)
                            <tr class="hover:bg-gray-50 dark:hover:bg-neutral-800" data-status="{{ $request->status }}">
                                <!-- Tipo y Nombre del Documento -->
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
                        
                                <!-- Solicitante -->
                                <td class="hidden sm:table-cell px-4 py-4">
                                    <div class="text-sm text-gray-800 dark:text-neutral-200">
                                        {{ $request->user->name }}
                                    </div>
                                </td>

                                <!-- Fecha -->
                                <td class="hidden sm:table-cell px-4 py-4">
                                    <div class="text-sm text-gray-800 dark:text-neutral-200">
                                        {{ $request->created_at->format('d/m/Y H:i') }}
                                    </div>
                                </td>
                        
                                <!-- Estado con clases dinámicas -->
                                <td class="px-4 py-4">
                                    <span class="inline-flex items-center gap-1.5 py-0.5 px-2 rounded-full text-xs font-medium {{ $statusClasses[$request->status] }}">
                                        {{ $statusLabels[$request->status] }}
                                    </span>
                                </td>
                        
                                <td class="px-6 py-4 align-middle text-end">
                                    @if($request->userLeaderType === 'primary' && $request->status === 'pendiente_lider')
                                        <button type="button"
                                                onclick="openModal('review-modal-{{ $request->id }}')"
                                                class="inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent text-blue-600 hover:text-blue-800 disabled:opacity-50 disabled:pointer-events-none dark:text-blue-500 dark:hover:text-blue-400">
                                            <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                                                <circle cx="12" cy="12" r="3"/>
                                            </svg>
                                            Revisar como Líder
                                        </button>
                                    @elseif($request->userLeaderType === 'secondary' && $request->status === 'pendiente_segundo_lider')
                                        <button type="button"
                                                onclick="openModal('second-leader-review-modal-{{ $request->id }}')"
                                                class="inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent text-blue-600 hover:text-blue-800 disabled:opacity-50 disabled:pointer-events-none dark:text-blue-500 dark:hover:text-blue-400">
                                            <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                                                <circle cx="12" cy="12" r="3"/>
                                            </svg>
                                            Revisar como Segundo Líder
                                        </button>
                                    @else
                                        <span class="text-sm text-gray-500 dark:text-gray-400">
                                            No disponible
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div class="px-6 py-4 grid gap-3 md:flex md:justify-between md:items-center border-t border-gray-200 dark:border-neutral-700">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-neutral-400">
                            Mostrando <span class="font-semibold text-gray-800 dark:text-neutral-200">{{$documentRequests->firstItem() }}</span> a 
                            <span class="font-semibold text-gray-800 dark:text-neutral-200">{{$documentRequests->lastItem() }}</span> de 
                            <span class="font-semibold text-gray-800 dark:text-neutral-200">{{$documentRequests->total() }}</span> resultados
                        </p>
                    </div>
                    <div>
                        {{$documentRequests->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modales -->
    @foreach($documentRequests as $request)
    <!-- Modal de Revisión del Líder Principal -->
    <div id="review-modal-{{ $request->id }}" 
         class="fixed inset-0 z-50 hidden"
         aria-labelledby="review-modal-title-{{ $request->id }}" 
         role="dialog" 
         aria-modal="true">
        <!-- Overlay con animación mejorada -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity duration-300 ease-in-out"></div>

        <!-- Contenedor Principal con animación -->
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-2xl transition-all duration-300 ease-in-out sm:my-8 sm:w-full sm:max-w-3xl dark:bg-neutral-800">
                    <!-- Header con diseño mejorado -->
                    <div class="border-b border-gray-200 bg-gray-50 px-6 py-4 dark:bg-neutral-700 dark:border-neutral-600">
                        <div class="flex items-center justify-between">
                            <h3 id="review-modal-title-{{ $request->id }}" 
                                class="text-lg font-semibold text-gray-900 dark:text-white">
                                Revisión de Documento (Líder Principal)
                            </h3>
                            <button type="button" 
                                    onclick="closeModal('review-modal-{{ $request->id }}')"
                                    class="rounded-lg p-1 text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:hover:bg-neutral-600"
                                    aria-label="Cerrar">
                                <svg class="size-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Contenido del Modal con mejor espaciado -->
                    <div class="px-6 py-4">
                        <div class="space-y-6">
                            <!-- Información del Documento con diseño mejorado -->
                            <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                                <div class="bg-gray-50 rounded-lg p-4 dark:bg-neutral-700">
                                    <h4 class="text-sm font-medium text-gray-500 dark:text-neutral-300">Documento</h4>
                                    <p class="mt-1 text-base font-medium text-gray-900 dark:text-white">{{ $request->document_name }}</p>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-4 dark:bg-neutral-700">
                                    <h4 class="text-sm font-medium text-gray-500 dark:text-neutral-300">Solicitante</h4>
                                    <p class="mt-1 text-base font-medium text-gray-900 dark:text-white">{{ $request->user->name }}</p>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-4 dark:bg-neutral-700">
                                    <h4 class="text-sm font-medium text-gray-500 dark:text-neutral-300">Tipo de Solicitud</h4>
                                    <p class="mt-1 text-base font-medium text-gray-900 dark:text-white">{{ $request->getRequestTypeLabel() }}</p>
                                </div>
                            </div>

                            <!-- Descripción -->
                            <div class="bg-gray-50 rounded-lg p-4 dark:bg-neutral-700">
                                <h4 class="text-sm font-medium text-gray-500 dark:text-neutral-300">Descripción del documento o de la Modificacion</h4>
                                <p class="mt-1 text-base text-gray-900 dark:text-white">{{ $request->description }}</p>
                            </div>

                            <!-- Previsualización del Documento mejorada -->
                            <div class="bg-white rounded-lg border border-gray-200 shadow-sm dark:bg-neutral-700 dark:border-neutral-600">
                                <div class="p-4">
                                    <div class="flex items-center justify-between">
                                        <h4 class="text-sm font-medium text-gray-500 dark:text-neutral-300">Archivo del Documento</h4>
                                        <div class="flex space-x-3">
                                            <a href="{{ route('documents.requests.preview-final', $request->id) }}" 
                                               target="_blank"
                                               class="inline-flex items-center gap-x-2 px-3 py-2 text-sm font-medium rounded-lg text-blue-600 hover:text-blue-800 hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:hover:bg-neutral-600">
                                                <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                                Previsualizar
                                            </a>
                                            <a href="{{ route('documents.requests.download-final', $request->id) }}" 
                                               class="inline-flex items-center gap-x-2 px-3 py-2 text-sm font-medium rounded-lg text-blue-600 hover:text-blue-800 hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:hover:bg-neutral-600">
                                                <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                                </svg>
                                                Descargar
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Campo de Observaciones mejorado -->
                            <div>
                                <label for="observations-{{ $request->id }}" 
                                       class="block text-sm font-medium text-gray-700 dark:text-neutral-200">
                                    Observaciones
                                </label>
                                <div class="mt-1">
                                    <textarea id="observations-{{ $request->id }}"
                                            rows="4"
                                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition duration-150 ease-in-out dark:bg-neutral-700 dark:border-neutral-600 dark:text-white dark:placeholder-neutral-400"
                                            placeholder="Ingrese sus observaciones detalladas aquí..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer con botones mejorados -->
                    <div class="bg-gray-50 px-6 py-4 flex justify-end gap-x-3 dark:bg-neutral-700">
                        <button type="button"
                                onclick="closeModal('review-modal-{{ $request->id }}')"
                                class="px-4 py-2 text-sm font-medium rounded-lg border border-gray-300 bg-white text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-neutral-600 dark:text-white dark:border-neutral-500 dark:hover:bg-neutral-500">
                            Cancelar
                        </button>
                        <button type="button"
                                onclick="openModal('reject-modal-{{$request->id }}')"
                                class="px-4 py-2 text-sm font-medium rounded-lg bg-red-600 text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                            <span class="flex items-center gap-x-2">
                                <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Rechazar
                            </span>
                        </button>
                        <button type="button"
                                onclick="openModal('approve-modal-{{ $request->id }}')"
                                class="px-4 py-2 text-sm font-medium rounded-lg bg-green-600 text-white shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                            <span class="flex items-center gap-x-2">
                                <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Aprobar
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal de Revisión del Segundo Líder -->
    <div id="second-leader-review-modal-{{ $request->id }}" 
        class="fixed inset-0 z-50 hidden"
        aria-labelledby="second-leader-review-modal-title-{{ $request->id }}" 
        role="dialog" 
        aria-modal="true">
       <!-- Overlay con animación mejorada -->
       <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity duration-300 ease-in-out"></div>

       <!-- Contenedor Principal con animación -->
       <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
           <div class="flex min-h-full items-center justify-center p-4">
               <div class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-2xl transition-all duration-300 ease-in-out sm:my-8 sm:w-full sm:max-w-3xl dark:bg-neutral-800">
                   <!-- Header con diseño mejorado -->
                   <div class="border-b border-gray-200 bg-blue-50 px-6 py-4 dark:bg-blue-900 dark:border-blue-800">
                       <div class="flex items-center justify-between">
                           <h3 id="second-leader-review-modal-title-{{ $request->id }}" 
                               class="text-lg font-semibold text-blue-900 dark:text-blue-100">
                               Revisión de Documento (Segundo Líder)
                           </h3>
                           <button type="button" 
                                   onclick="closeModal('second-leader-review-modal-{{ $request->id }}')"
                                   class="rounded-lg p-1 text-blue-400 hover:text-blue-500 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:hover:bg-blue-800"
                                   aria-label="Cerrar">
                               <svg class="size-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                               </svg>
                           </button>
                       </div>
                   </div>

                   <!-- Contenido del Modal con mejor espaciado -->
                   <div class="px-6 py-4">
                       <div class="space-y-6">
                           <!-- Información del Documento con diseño mejorado -->
                           <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                               <div class="bg-blue-50 rounded-lg p-4 dark:bg-blue-900">
                                   <h4 class="text-sm font-medium text-blue-700 dark:text-blue-300">Documento</h4>
                                   <p class="mt-1 text-base font-medium text-blue-900 dark:text-white">{{ $request->document_name }}</p>
                               </div>
                               <div class="bg-blue-50 rounded-lg p-4 dark:bg-blue-900">
                                   <h4 class="text-sm font-medium text-blue-700 dark:text-blue-300">Solicitante</h4>
                                   <p class="mt-1 text-base font-medium text-blue-900 dark:text-white">{{ $request->user->name }}</p>
                               </div>
                               <div class="bg-blue-50 rounded-lg p-4 dark:bg-blue-900">
                                   <h4 class="text-sm font-medium text-blue-700 dark:text-blue-300">Tipo de Solicitud</h4>
                                   <p class="mt-1 text-base font-medium text-blue-900 dark:text-white">{{ $request->getRequestTypeLabel() }}</p>
                               </div>
                           </div>

                           <!-- Observaciones del primer líder -->
                           @if($request->leader_observations)
                           <div class="bg-green-50 rounded-lg p-4 dark:bg-green-900">
                               <h4 class="text-sm font-medium text-green-700 dark:text-green-300">Observaciones del Líder Principal</h4>
                               <p class="mt-1 text-base text-green-900 dark:text-white">{{ $request->leader_observations }}</p>
                               <p class="mt-2 text-xs text-green-600 dark:text-green-400">
                                   Aprobado el {{ $request->leader_approval_date->format('d/m/Y H:i') }}
                               </p>
                           </div>
                           @endif

                           <!-- Descripción -->
                           <div class="bg-blue-50 rounded-lg p-4 dark:bg-blue-900">
                               <h4 class="text-sm font-medium text-blue-700 dark:text-blue-300">Descripción del documento o de la Modificacion</h4>
                               <p class="mt-1 text-base text-blue-900 dark:text-white">{{ $request->description }}</p>
                           </div>

                           <<!-- Previsualización del Documento mejorada -->
                            <div class="bg-white rounded-lg border border-blue-200 shadow-sm dark:bg-neutral-700 dark:border-blue-800">
                                <div class="p-4">
                                    <div class="flex items-center justify-between">
                                        <h4 class="text-sm font-medium text-blue-700 dark:text-blue-300">Archivo del Documento</h4>
                                        <div class="flex space-x-3">
                                            <a href="{{ route('documents.requests.preview-final', $request->id) }}" 
                                               target="_blank"
                                               class="inline-flex items-center gap-x-2 px-3 py-2 text-sm font-medium rounded-lg text-blue-600 hover:text-blue-800 hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:hover:bg-blue-800">
                                                <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                                Previsualizar
                                            </a>
                                            <a href="{{ route('documents.requests.download-final', $request->id) }}" 
                                               class="inline-flex items-center gap-x-2 px-3 py-2 text-sm font-medium rounded-lg text-blue-600 hover:text-blue-800 hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:hover:bg-blue-800">
                                                <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                                </svg>
                                                Descargar
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Campo de Observaciones del segundo líder -->
                            <div>
                                <label for="second-leader-observations-{{ $request->id }}"
                                       class="block text-sm font-medium text-blue-700 dark:text-blue-300">
                                    Observaciones del Segundo Líder
                                </label>
                                <div class="mt-1">
                                    <textarea id="second-leader-observations-{{ $request->id }}"
                                            rows="4"
                                            class="block w-full rounded-lg border-blue-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition duration-150 ease-in-out dark:bg-blue-800 dark:border-blue-700 dark:text-white dark:placeholder-blue-400"
                                            placeholder="Ingrese sus observaciones detalladas aquí como segundo líder..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer con botones mejorados -->
                    <div class="bg-blue-50 px-6 py-4 flex justify-end gap-x-3 dark:bg-blue-900">
                        <button type="button"
                                onclick="closeModal('second-leader-review-modal-{{ $request->id }}')"
                                class="px-4 py-2 text-sm font-medium rounded-lg border border-blue-300 bg-white text-blue-700 shadow-sm hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-blue-800 dark:text-white dark:border-blue-700 dark:hover:bg-blue-700">
                            Cancelar
                        </button>
                        <button type="button"
                                onclick="openModal('second-leader-reject-modal-{{$request->id }}')"
                                class="px-4 py-2 text-sm font-medium rounded-lg bg-red-600 text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                            <span class="flex items-center gap-x-2">
                                <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Rechazar
                            </span>
                        </button>
                        <button type="button"
                                onclick="openModal('second-leader-approve-modal-{{ $request->id }}')"
                                class="px-4 py-2 text-sm font-medium rounded-lg bg-green-600 text-white shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                            <span class="flex items-center gap-x-2">
                                <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Aprobar
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Aprobación del Líder Principal -->
    <div id="approve-modal-{{ $request->id }}" 
         class="fixed inset-0 z-[60] hidden"
         aria-labelledby="approve-modal-title-{{ $request->id }}" 
         role="dialog" 
         aria-modal="true">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity duration-300 ease-in-out"></div>

        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-2xl transition-all sm:max-w-lg w-full dark:bg-neutral-800">
                    <div class="px-6 pt-6 pb-4">
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0">
                                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-green-100">
                                    <svg class="h-6 w-6 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="mt-1">
                                <h3 id="approve-modal-title-{{ $request->id }}" class="text-lg font-semibold text-gray-900 dark:text-white">
                                    Confirmar Aprobación (Líder Principal)
                                </h3>
                                <div class="mt-4">
                                    <label for="approve-observations-{{ $request->id }}" 
                                           class="block text-sm font-medium text-gray-700 dark:text-neutral-200">
                                        Observaciones (Opcional)
                                    </label>
                                    <textarea id="approve-observations-{{ $request->id }}"
                                            rows="4"
                                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm transition duration-150 ease-in-out dark:bg-neutral-700 dark:border-neutral-600 dark:text-white dark:placeholder-neutral-400"
                                            placeholder="Agregue observaciones adicionales si lo desea..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-6 py-4 flex justify-end space-x-3 dark:bg-neutral-700">
                        <button type="button"
                                onclick="closeModal('approve-modal-{{ $request->id }}')"
                                class="px-4 py-2 text-sm font-medium rounded-lg border border-gray-300 bg-white text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-500 dark:bg-neutral-600 dark:text-white dark:border-neutral-500 dark:hover:bg-neutral-500">
                            Cancelar
                        </button>
                        <form action="{{ route('documents.requests.leader-approve', $request->id) }}" 
                              method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="observations" id="hidden-approve-observations-{{ $request->id }}">
                            <button type="submit"
                                    onclick="return prepareApprovalSubmit({{ $request->id }})"
                                    class="px-4 py-2 text-sm font-medium rounded-lg bg-green-600 text-white shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                                <span class="flex items-center gap-x-2">
                                    <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Confirmar Aprobación
                                </span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Rechazo del Líder Principal -->
    <div id="reject-modal-{{ $request->id }}" 
         class="fixed inset-0 z-[60] hidden"
         aria-labelledby="reject-modal-title-{{ $request->id }}" 
         role="dialog" 
         aria-modal="true">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity duration-300 ease-in-out"></div>

        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-2xl transition-all sm:max-w-lg w-full dark:bg-neutral-800">
                    <div class="px-6 pt-6 pb-4">
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0">
                                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-red-100">
                                    <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="mt-1">
                                <h3 id="reject-modal-title-{{ $request->id }}" class="text-lg font-semibold text-gray-900 dark:text-white">
                                    Confirmar Rechazo (Líder Principal)
                                </h3>
                                <p class="mt-2 text-sm text-gray-500 dark:text-neutral-400">
                                    Por favor, indique el motivo detallado del rechazo:
                                </p>
                                <div class="mt-4">
                                    <textarea id="reject-reason-{{ $request->id }}"
                                            rows="4"
                                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm transition duration-150 ease-in-out dark:bg-neutral-700 dark:border-neutral-600 dark:text-white dark:placeholder-neutral-400"
                                            required
                                            placeholder="Detalle los motivos del rechazo..."></textarea>
                                    <p class="mt-2 text-sm text-gray-500 dark:text-neutral-400">
                                        Esta información será enviada al solicitante.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-6 py-4 flex justify-end space-x-3 dark:bg-neutral-700">
                        <button type="button"
                                onclick="closeModal('reject-modal-{{ $request->id }}')"
                                class="px-4 py-2 text-sm font-medium rounded-lg border border-gray-300 bg-white text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-red-500 dark:bg-neutral-600 dark:text-white dark:border-neutral-500 dark:hover:bg-neutral-500">
                            Cancelar
                        </button>
                        <form action="{{ route('documents.requests.leader-reject', $request->id) }}" 
                              method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="observations" id="hidden-reject-observations-{{ $request->id }}">
                            <button type="submit"
                                    onclick="return prepareRejectSubmit({{ $request->id }})"
                                    class="px-4 py-2 text-sm font-medium rounded-lg bg-red-600 text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                                <span class="flex items-center gap-x-2">
                                    <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    Confirmar Rechazo
                                </span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Aprobación del Segundo Líder -->
    <div id="second-leader-approve-modal-{{ $request->id }}" 
         class="fixed inset-0 z-[60] hidden"
         aria-labelledby="second-leader-approve-modal-title-{{ $request->id }}" 
         role="dialog" 
         aria-modal="true">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity duration-300 ease-in-out"></div>

        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-2xl transition-all sm:max-w-lg w-full dark:bg-neutral-800">
                    <div class="px-6 pt-6 pb-4">
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0">
                                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-100">
                                    <svg class="h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="mt-1">
                                <h3 id="second-leader-approve-modal-title-{{ $request->id }}" class="text-lg font-semibold text-gray-900 dark:text-white">
                                    Confirmar Aprobación (Segundo Líder)
                                </h3>
                                <div class="mt-4">
                                    <label for="second-leader-approve-observations-{{ $request->id }}" 
                                           class="block text-sm font-medium text-gray-700 dark:text-neutral-200">
                                        Observaciones (Opcional)
                                    </label>
                                    <textarea id="second-leader-approve-observations-{{ $request->id }}"
                                            rows="4"
                                            class="mt-1 block w-full rounded-lg border-blue-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition duration-150 ease-in-out dark:bg-blue-900 dark:border-blue-700 dark:text-white dark:placeholder-blue-400"
                                            placeholder="Agregue observaciones adicionales si lo desea..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-blue-50 px-6 py-4 flex justify-end space-x-3 dark:bg-blue-900">
                        <button type="button"
                                onclick="closeModal('second-leader-approve-modal-{{ $request->id }}')"
                                class="px-4 py-2 text-sm font-medium rounded-lg border border-blue-300 bg-white text-blue-700 shadow-sm hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-blue-800 dark:text-white dark:border-blue-700 dark:hover:bg-blue-700">
                            Cancelar
                        </button>
                        <form action="{{ route('documents.requests.second-leader-approve', $request->id) }}" 
                              method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="observations" id="hidden-second-leader-approve-observations-{{ $request->id }}">
                            <button type="submit"
                                    onclick="return prepareSecondLeaderApprovalSubmit({{ $request->id }})"
                                    class="px-4 py-2 text-sm font-medium rounded-lg bg-blue-600 text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <span class="flex items-center gap-x-2">
                                    <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Confirmar Aprobación
                                </span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Rechazo del Segundo Líder -->
    <div id="second-leader-reject-modal-{{ $request->id }}" 
         class="fixed inset-0 z-[60] hidden"
         aria-labelledby="second-leader-reject-modal-title-{{ $request->id }}" 
         role="dialog" 
         aria-modal="true">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity duration-300 ease-in-out"></div>

        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-2xl transition-all sm:max-w-lg w-full dark:bg-neutral-800">
                    <div class="px-6 pt-6 pb-4">
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0">
                                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-red-100">
                                    <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="mt-1">
                                <h3 id="second-leader-reject-modal-title-{{ $request->id }}" class="text-lg font-semibold text-gray-900 dark:text-white">
                                    Confirmar Rechazo (Segundo Líder)
                                </h3>
                                <p class="mt-2 text-sm text-gray-500 dark:text-neutral-400">
                                    Por favor, indique el motivo detallado del rechazo:
                                </p>
                                <div class="mt-4">
                                    <textarea id="second-leader-reject-reason-{{ $request->id }}"
                                            rows="4"
                                            class="mt-1 block w-full rounded-lg border-red-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm transition duration-150 ease-in-out dark:bg-red-900 dark:border-red-800 dark:text-white dark:placeholder-red-400"
                                            required
                                            placeholder="Detalle los motivos del rechazo..."></textarea>
                                    <p class="mt-2 text-sm text-gray-500 dark:text-neutral-400">
                                        Esta información será enviada al solicitante.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-red-50 px-6 py-4 flex justify-end space-x-3 dark:bg-red-900">
                        <button type="button"
                                onclick="closeModal('second-leader-reject-modal-{{ $request->id }}')"
                                class="px-4 py-2 text-sm font-medium rounded-lg border border-red-300 bg-white text-red-700 shadow-sm hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 dark:bg-red-800 dark:text-white dark:border-red-700 dark:hover:bg-red-700">
                            Cancelar
                        </button>
                        <form action="{{ route('documents.requests.leader-reject', $request->id) }}" 
                              method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="observations" id="hidden-second-leader-reject-observations-{{ $request->id }}">
                            <button type="submit"
                                    onclick="return prepareSecondLeaderRejectSubmit({{ $request->id }})"
                                    class="px-4 py-2 text-sm font-medium rounded-lg bg-red-600 text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                                <span class="flex items-center gap-x-2">
                                    <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    Confirmar Rechazo
                                </span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection

@push('scripts')
<script>
    // Namespace para las funciones de la aplicación
    const DocumentManagement = {
        // Gestión de modales
        modals: {
            open(modalId) {
                const modal = document.getElementById(modalId);
                if (!modal) return;

                // Cerrar otros modales abiertos
                this.closeOthers(modalId);

                // Mostrar el modal actual
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';

                // Copiar observaciones si es modal de aprobación
                if (modalId.startsWith('approve-modal-')) {
                    const documentId = modalId.split('-')[2];
                    const observations = document.getElementById(`observations-${documentId}`)?.value || '';
                    const approveObservations = document.getElementById(`approve-observations-${documentId}`);
                    if (approveObservations) {
                        approveObservations.value = observations;
                        // Enfocar el campo de observaciones
                        setTimeout(() => approveObservations.focus(), 100);
                    }
                }
                
                // Copiar observaciones si es modal de aprobación del segundo líder
                if (modalId.startsWith('second-leader-approve-modal-')) {
                    const documentId = modalId.split('-')[4];
                    const observations = document.getElementById(`second-leader-observations-${documentId}`)?.value || '';
                    const approveObservations = document.getElementById(`second-leader-approve-observations-${documentId}`);
                    if (approveObservations) {
                        approveObservations.value = observations;
                        // Enfocar el campo de observaciones
                        setTimeout(() => approveObservations.focus(), 100);
                    }
                }

                // Enfocar el campo de rechazo si es el modal correspondiente
                if (modalId.startsWith('reject-modal-')) {
                    const documentId = modalId.split('-')[2];
                    const rejectReason = document.getElementById(`reject-reason-${documentId}`);
                    if (rejectReason) {
                        setTimeout(() => rejectReason.focus(), 100);
                    }
                }
                
                // Enfocar el campo de rechazo si es el modal correspondiente del segundo líder
                if (modalId.startsWith('second-leader-reject-modal-')) {
                    const documentId = modalId.split('-')[4];
                    const rejectReason = document.getElementById(`second-leader-reject-reason-${documentId}`);
                    if (rejectReason) {
                        setTimeout(() => rejectReason.focus(), 100);
                    }
                }
            },

            close(modalId) {
                const modal = document.getElementById(modalId);
                if (!modal) return;

                modal.classList.add('hidden');
                
                // Restaurar scroll si no hay más modales abiertos
                const openModals = document.querySelectorAll('[role="dialog"]:not(.hidden)').length;
                if (openModals === 0) {
                    document.body.style.overflow = 'auto';
                }
            },

            closeOthers(currentModalId) {
                document.querySelectorAll('[role="dialog"]:not(.hidden)').forEach(modal => {
                    if (modal.id !== currentModalId) {
                        this.close(modal.id);
                    }
                });
            },

            closeAll() {
                document.querySelectorAll('[role="dialog"]:not(.hidden)').forEach(modal => {
                    this.close(modal.id);
                });
            }
        },

        // Gestión de formularios
        forms: {
            prepareApprovalSubmit(requestId) {
                const observations = document.getElementById(`approve-observations-${requestId}`)?.value || '';
                const hiddenInput = document.getElementById(`hidden-approve-observations-${requestId}`);
                if (hiddenInput) {
                    hiddenInput.value = observations;
                }
                return true;
            },

            prepareRejectSubmit(requestId) {
                const reason = document.getElementById(`reject-reason-${requestId}`)?.value || '';
                if (!reason.trim()) {
                    alert('Por favor, indique el motivo del rechazo');
                    const rejectReason = document.getElementById(`reject-reason-${requestId}`);
                    if (rejectReason) {
                        rejectReason.focus();
                    }
                    return false;
                }

                const hiddenInput = document.getElementById(`hidden-reject-observations-${requestId}`);
                if (hiddenInput) {
                    hiddenInput.value = reason;
                }
                return true;
            },
            
            prepareSecondLeaderApprovalSubmit(requestId) {
                const observations = document.getElementById(`second-leader-approve-observations-${requestId}`)?.value || '';
                const hiddenInput = document.getElementById(`hidden-second-leader-approve-observations-${requestId}`);
                if (hiddenInput) {
                    hiddenInput.value = observations;
                }
                return true;
            },

            prepareSecondLeaderRejectSubmit(requestId) {
                const reason = document.getElementById(`second-leader-reject-reason-${requestId}`)?.value || '';
                if (!reason.trim()) {
                    alert('Por favor, indique el motivo del rechazo');
                    const rejectReason = document.getElementById(`second-leader-reject-reason-${requestId}`);
                    if (rejectReason) {
                        rejectReason.focus();
                    }
                    return false;
                }

                const hiddenInput = document.getElementById(`hidden-second-leader-reject-observations-${requestId}`);
                if (hiddenInput) {
                    hiddenInput.value = reason;
                }
                return true;
            },

            validateRejectForm(form) {
                const rejectReason = form.querySelector('textarea[name="reject_reason"]');
                if (rejectReason && !rejectReason.value.trim()) {
                    alert('Por favor, indique el motivo del rechazo.');
                    rejectReason.focus();
                    return false;
                }
                return true;
            }
        },

        // Gestión de filtros y búsqueda
        filters: {
            init() {
                this.searchInput = document.getElementById('search');
                this.statusFilter = document.getElementById('status-filter');

                if (this.searchInput) {
                    this.searchInput.addEventListener('input', () => this.applyFilters());
                }

                if (this.statusFilter) {
                    this.statusFilter.addEventListener('change', () => this.applyFilters());
                }
            },

            applyFilters() {
                const searchTerm = this.searchInput?.value.toLowerCase() || '';
                const statusTerm = this.statusFilter?.value.toLowerCase() || '';

                document.querySelectorAll('tbody tr').forEach(row => {
                    const text = row.textContent.toLowerCase();
                    const status = row.getAttribute('data-status') || '';

                    const matchesSearch = text.includes(searchTerm);
                    const matchesStatus = !statusTerm || status === statusTerm;

                    row.style.display = matchesSearch && matchesStatus ? '' : 'none';
                });
            }
        },

        // Inicialización de event listeners
        init() {
            // Event listeners para cerrar con Escape
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    this.modals.closeAll();
                }
            });

            // Event listeners para cerrar al hacer clic fuera
            document.addEventListener('click', (e) => {
                if (e.target.getAttribute('role') === 'dialog') {
                    this.modals.close(e.target.id);
                }
            });

            // Validación de formularios
            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', (e) => {
                    if (!this.forms.validateRejectForm(form)) {
                        e.preventDefault();
                    }
                });
            });

            // Inicializar filtros
            this.filters.init();
        }
    };

    // Funciones globales necesarias para los eventos onclick en HTML
    function openModal(modalId) {
        DocumentManagement.modals.open(modalId);
    }

    function closeModal(modalId) {
        DocumentManagement.modals.close(modalId);
    }

    function prepareApprovalSubmit(requestId) {
        return DocumentManagement.forms.prepareApprovalSubmit(requestId);
    }

    function prepareRejectSubmit(requestId) {
        return DocumentManagement.forms.prepareRejectSubmit(requestId);
    }
    
    function prepareSecondLeaderApprovalSubmit(requestId) {
        return DocumentManagement.forms.prepareSecondLeaderApprovalSubmit(requestId);
    }
    
    function prepareSecondLeaderRejectSubmit(requestId) {
        return DocumentManagement.forms.prepareSecondLeaderRejectSubmit(requestId);
    }

    // Inicializar cuando el DOM esté listo
    document.addEventListener('DOMContentLoaded', () => {
        DocumentManagement.init();
    });
</script>
@endpush

@push('styles')
<style>
    /* Transiciones para modales */
    [role="dialog"] {
        transition: opacity 0.3s ease-out;
    }

    [role="dialog"]:not(.hidden) {
        opacity: 1;
    }

    [role="dialog"].hidden {
        opacity: 0;
    }

    /* Estilos para estados */
    .status-badge {
        @apply inline-flex items-center gap-1.5 py-0.5 px-2 rounded-full text-xs font-medium;
    }
    
    .status-pending-primary {
        @apply bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300;
    }
    
    .status-pending-secondary {
        @apply bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300;
    }
    
    .status-review {
        @apply bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300;
    }
    
    .status-approved {
        @apply bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300;
    }
    
    .status-rejected {
        @apply bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300;
    }

    /* Estilos para dark mode */
    .dark .modal-content {
        @apply bg-neutral-800 border-neutral-700;
    }

    .dark .modal-header {
        @apply border-neutral-700;
    }

    /* Animaciones suaves para interacciones */
    button {
        transition: all 0.2s ease-in-out;
    }

    /* Estilos para hover en filas de tabla */
    tr {
        transition: background-color 0.2s ease-in-out;
    }

    /* Personalización de scrollbar */
    .overflow-y-auto {
        scrollbar-width: thin;
        scrollbar-color: rgba(156, 163, 175, 0.5) transparent;
    }

    .overflow-y-auto::-webkit-scrollbar {
        width: 6px;
    }

    .overflow-y-auto::-webkit-scrollbar-track {
        background: transparent;
    }

    .overflow-y-auto::-webkit-scrollbar-thumb {
        background-color: rgba(156, 163, 175, 0.5);
        border-radius: 3px;
    }

    .overflow-y-auto::-webkit-scrollbar-thumb:hover {
        background-color: rgba(156, 163, 175, 0.7);
    }

    /* Estilos para scrollbar en modo oscuro */
    .dark .overflow-y-auto {
        scrollbar-color: rgba(75, 85, 99, 0.5) transparent;
    }

    .dark .overflow-y-auto::-webkit-scrollbar-thumb {
        background-color: rgba(75, 85, 99, 0.5);
    }

    .dark .overflow-y-auto::-webkit-scrollbar-thumb:hover {
        background-color: rgba(75, 85, 99, 0.7);
    }

    /* Estilos para inputs y textareas */
    input, textarea {
        transition: border-color 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }

    /* Estilos para los botones de acción */
    .action-button {
        @apply inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg px-3 py-2 transition-colors duration-200;
    }

    .action-button-primary {
        @apply bg-blue-600 text-white hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2;
    }

    .action-button-success {
        @apply bg-green-600 text-white hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2;
    }

    .action-button-danger {
        @apply bg-red-600 text-white hover:bg-red-700 focus:ring-2 focus:ring-red-500 focus:ring-offset-2;
    }

    .action-button-secondary {
        @apply bg-white text-gray-700 border border-gray-300 hover:bg-gray-50 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2;
    }

    /* Estilos específicos para los modales del segundo líder */
    .second-leader-modal-header {
        @apply bg-blue-50 border-blue-200 dark:bg-blue-900 dark:border-blue-800;
    }
    
    .second-leader-modal-footer {
        @apply bg-blue-50 dark:bg-blue-900;
    }
    
    /* Estilos para indicar el estado de pendiente del segundo líder */
    .pending-second-leader {
        @apply bg-yellow-100 text-yellow-800 border-yellow-200
               dark:bg-yellow-900 dark:text-yellow-300 dark:border-yellow-800;
    }
    
    /* Animaciones específicas para modales del segundo líder */
    [id^="second-leader"] {
        transition: transform 0.3s ease-in-out, opacity 0.3s ease-in-out;
    }
    
    [id^="second-leader"]:not(.hidden) {
        transform: scale(1);
        opacity: 1;
    }
    
    [id^="second-leader"].hidden {
        transform: scale(0.95);
        opacity: 0;
    }
</style>
@endpush