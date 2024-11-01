@extends('layouts.app')

@section('title', 'Detalles de la Plantilla')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-6">
    <div class="bg-white rounded-xl shadow dark:bg-neutral-800">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200 dark:border-neutral-700">
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white">
                    Detalles de la Plantilla
                </h2>
                <a href="{{ route('document-templates.index') }}" 
                   class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:text-white dark:hover:bg-neutral-700 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600">
                    <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m12 19-7-7 7-7"/>
                        <path d="M19 12H5"/>
                    </svg>
                    Volver
                </a>
            </div>
        </div>

        <!-- Contenido -->
        <div class="p-6">
            <!-- Información Básica -->
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-800 dark:text-white mb-4">
                    Información Básica
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nombre -->
                    <div class="bg-gray-50 rounded-lg p-4 dark:bg-neutral-700">
                        <div class="text-sm font-medium text-gray-500 dark:text-neutral-400 mb-1">
                            Nombre
                        </div>
                        <div class="text-base text-gray-900 dark:text-white">
                            {{ $template->name }}
                        </div>
                    </div>

                    <!-- Estado -->
                    <div class="bg-gray-50 rounded-lg p-4 dark:bg-neutral-700">
                        <div class="text-sm font-medium text-gray-500 dark:text-neutral-400 mb-1">
                            Estado
                        </div>
                        <div>
                            <span class="inline-flex items-center gap-1.5 py-0.5 px-2 rounded-full text-xs font-medium {{ $template->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                @if($template->is_active)
                                    <svg class="size-2.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 2c5.523 0 10 4.477 10 10s-4.477 10-10 10S2 17.523 2 12 6.477 2 12 2z"/>
                                        <path d="m9 12 2 2 4-4"/>
                                    </svg>
                                    Activo
                                @else
                                    <svg class="size-2.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10"/>
                                        <line x1="15" x2="9" y1="9" y2="15"/>
                                        <line x1="9" x2="15" y1="9" y2="15"/>
                                    </svg>
                                    Inactivo
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Descripción -->
            @if($template->description)
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-800 dark:text-white mb-4">
                    Descripción
                </h3>
                <div class="bg-gray-50 rounded-lg p-4 dark:bg-neutral-700">
                    <p class="text-gray-700 dark:text-neutral-200 whitespace-pre-line">
                        {{ $template->description }}
                    </p>
                </div>
            </div>
            @endif

            <!-- Archivo -->
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-800 dark:text-white mb-4">
                    Archivo
                </h3>
                <div class="bg-gray-50 rounded-lg p-4 dark:bg-neutral-700">
                    <div class="flex items-center gap-x-4">
                        <svg class="size-8 text-gray-500" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/>
                            <polyline points="14 2 14 8 20 8"/>
                        </svg>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ basename($template->file_path) }}
                            </p>
                            <p class="text-sm text-gray-500 dark:text-neutral-400">
                                Subido el {{ $template->created_at->format('d/m/Y H:i') }}
                            </p>
                        </div>
                        <a href="{{ route('document-templates.download', $template->id) }}" 
                           class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:text-white dark:hover:bg-neutral-700 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600">
                            <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                <polyline points="7 10 12 15 17 10"/>
                                <line x1="12" x2="12" y1="15" y2="3"/>
                            </svg>
                            Descargar
                        </a>
                    </div>
                </div>
            </div>

            <!-- Información Adicional -->
            <div>
                <h3 class="text-lg font-medium text-gray-800 dark:text-white mb-4">
                    Información Adicional
                </h3>
                <div class="bg-gray-50 rounded-lg p-4 dark:bg-neutral-700 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <span class="block text-sm font-medium text-gray-500 dark:text-neutral-400">
                                Fecha de Creación
                            </span>
                            <span class="block text-sm text-gray-900 dark:text-white">
                                {{ $template->created_at->format('d/m/Y H:i') }}
                            </span>
                        </div>
                        <div>
                            <span class="block text-sm font-medium text-gray-500 dark:text-neutral-400">
                                Última Actualización
                            </span>
                            <span class="block text-sm text-gray-900 dark:text-white">
                                {{ $template->updated_at->format('d/m/Y H:i') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="mt-8 flex justify-end gap-x-3">
                <a href="{{ route('document-templates.edit', $template->id) }}" 
                   class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600">
                    <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/>
                        <path d="m15 5 4 4"/>
                    </svg>
                    Editar Plantilla
                </a>
            </div>
        </div>
    </div>
</div>
@endsection