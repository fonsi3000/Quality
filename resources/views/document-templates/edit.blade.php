@extends('layouts.app')

@section('title', 'Editar Plantilla')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-6">
    <div class="bg-white rounded-xl shadow dark:bg-neutral-800">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200 dark:border-neutral-700">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white">
                Editar Plantilla: {{ $template->name }}
            </h2>
        </div>

        <!-- Formulario -->
        <form action="{{ route('document-templates.update', $template->id) }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            @method('PUT')

            <!-- Mensajes de Error -->
            @if ($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 text-sm text-red-600 rounded-lg p-4 dark:bg-red-800/10 dark:border-red-900 dark:text-red-500" role="alert">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Nombre -->
            <div class="mb-6">
                <label for="name" class="block mb-2 text-sm font-medium text-gray-800 dark:text-white">
                    Nombre de la Plantilla
                    <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       id="name" 
                       name="name" 
                       required
                       value="{{ old('name', $template->name) }}"
                       class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:focus:ring-gray-600"
                       placeholder="Ingrese el nombre de la plantilla">
            </div>

            <!-- Descripción -->
            <div class="mb-6">
                <label for="description" class="block mb-2 text-sm font-medium text-gray-800 dark:text-white">
                    Descripción
                </label>
                <textarea id="description" 
                          name="description" 
                          rows="4"
                          class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:focus:ring-gray-600"
                          placeholder="Ingrese una descripción de la plantilla">{{ old('description', $template->description) }}</textarea>
            </div>

            <!-- Archivo Actual -->
            <div class="mb-6">
                <div class="text-sm font-medium text-gray-800 dark:text-white mb-2">
                    Archivo Actual
                </div>
                <div class="flex items-center gap-x-3 p-4 rounded-lg border border-gray-200 dark:border-neutral-700">
                    <svg class="size-8 text-gray-500" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/>
                        <polyline points="14 2 14 8 20 8"/>
                    </svg>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-800 truncate dark:text-white">
                            {{ basename($template->file_path) }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-neutral-400">
                            Subido el {{ $template->created_at->format('d/m/Y H:i') }}
                        </p>
                    </div>
                    <a href="{{ route('document-templates.download', $template->id) }}" 
                       class="text-sm text-blue-600 hover:text-blue-700 dark:text-blue-500 dark:hover:text-blue-400">
                        Descargar
                    </a>
                </div>
            </div>

            <!-- Upload New File -->
            <div class="mb-6">
                <label for="file" class="block mb-2 text-sm font-medium text-gray-800 dark:text-white">
                    Nuevo Archivo (opcional)
                </label>
                <div class="mt-2 flex justify-center rounded-lg border border-dashed border-gray-200 px-6 py-10 dark:border-neutral-700">
                    <div class="text-center">
                        <!-- Upload Icon -->
                        <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 14.899A7 7 0 1 1 15.71 8h1.79a4.5 4.5 0 0 1 2.5 8.242"/>
                            <path d="M12 12v9"/>
                            <path d="m16 16-4-4-4 4"/>
                        </svg>
                        <div class="mt-4 flex text-sm leading-6 text-gray-600 dark:text-neutral-400">
                            <label for="file" class="relative cursor-pointer rounded-md bg-white dark:bg-neutral-800 font-semibold text-blue-600 focus-within:outline-none focus-within:ring-2 focus-within:ring-blue-600 focus-within:ring-offset-2 hover:text-blue-500">
                                <span>Seleccione un archivo</span>
                                <input id="file" 
                                       name="file" 
                                       type="file" 
                                       class="sr-only">
                            </label>
                            <p class="pl-1">o arrastre y suelte aquí</p>
                        </div>
                        <p class="text-xs leading-5 text-gray-600 dark:text-neutral-400">
                            Se permiten archivos de hasta 50MB
                        </p>
                        <!-- Preview del archivo seleccionado -->
                        <div id="file-preview" class="mt-4 hidden">
                            <p class="text-sm text-gray-500 dark:text-neutral-400">
                                Archivo seleccionado: <span id="file-name" class="font-medium"></span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Estado -->
            <div class="mb-6">
                <div class="flex items-center">
                    <input type="checkbox" 
                           id="is_active" 
                           name="is_active" 
                           value="1"
                           {{ old('is_active', $template->is_active) ? 'checked' : '' }}
                           class="border-gray-200 rounded text-blue-600 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800">
                    <label for="is_active" class="text-sm text-gray-500 ms-3 dark:text-neutral-400">
                        Plantilla Activa
                    </label>
                </div>
            </div>

            <!-- Botones -->
            <div class="flex items-center justify-end gap-x-3">
                <a href="{{ route('document-templates.index') }}" 
                   class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:text-white dark:hover:bg-neutral-700 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600">
                    Cancelar
                </a>
                <button type="submit" 
                        class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600">
                    <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                        <polyline points="17 21 17 13 7 13 7 21"/>
                        <polyline points="7 3 7 8 15 8"/>
                    </svg>
                    Actualizar Plantilla
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('file');
    const filePreview = document.getElementById('file-preview');
    const fileName = document.getElementById('file-name');

    fileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            fileName.textContent = file.name;
            filePreview.classList.remove('hidden');
        } else {
            filePreview.classList.add('hidden');
        }
    });

    // Drag and drop functionality
    const dropZone = document.querySelector('.border-dashed');
    
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults (e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, highlight, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, unhighlight, false);
    });

    function highlight(e) {
        dropZone.classList.add('border-blue-500', 'bg-blue-50', 'dark:bg-blue-900/10');
    }

    function unhighlight(e) {
        dropZone.classList.remove('border-blue-500', 'bg-blue-50', 'dark:bg-blue-900/10');
    }

    dropZone.addEventListener('drop', handleDrop, false);

    function handleDrop(e) {
        const dt = e.dataTransfer;
        const file = dt.files[0];
        fileInput.files = dt.files;
        if (file) {
            fileName.textContent = file.name;
            filePreview.classList.remove('hidden');
        }
    }
});
</script>
@endpush

@endsection