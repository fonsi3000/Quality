@extends('layouts.app')

@section('title', 'Nuevo Documento')

@section('content')
    <div class="flex flex-col">
        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                @if ($errors->any())
                    <div class="mb-6">
                        <div class="p-4 border border-red-200 rounded-lg bg-red-50 dark:bg-red-900/50 dark:border-red-800">
                            <ul class="list-disc list-inside text-sm text-red-600 dark:text-red-400">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <div class="bg-white dark:bg-neutral-800 overflow-hidden shadow-sm rounded-lg">
                    <div class="p-8">
                        <!-- Encabezado -->
                        <div class="mb-8">
                            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">
                                Nuevo Documento
                            </h2>
                            <p class="mt-1 text-sm text-gray-600 dark:text-neutral-400">
                                Complete los campos para crear un nuevo documento
                            </p>
                        </div>

                        <!-- Formulario -->
                        <form action="{{ route('documents.requests.store') }}" method="POST" enctype="multipart/form-data"
                            class="space-y-6" id="documentForm">
                            @csrf

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                               

                                <!-- Proceso -->
                                <div class="space-y-2">
                                    <label for="process_id"
                                        class="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                        Origen del Proceso <span class="text-red-500">*</span>
                                    </label>

                                    <select id="process_id" name="process_id"
                                        class="py-2 px-3 block w-full border border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400"
                                        required>
                                        <option value="">Seleccione un proceso</option>
                                        @foreach ($processes as $pr)
                                            <option value={{ $pr->id }}>{{ $pr->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <!-- Usuario solicitante -->
                                <div class="space-y-2">
                                    <label for="user_id"
                                        class="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                        Usuario solicitante <span class="text-red-500">*</span>
                                    </label>

                                    <select id="user_id" name="user_id"
                                        class="py-2 px-3 block w-full border border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400"
                                        required>
                                        <option value="">Seleccione un usuario</option>
                                        @foreach ($users as $user)
                                            <option value={{ $user->id }}>{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <!-- Documento Existente (Solo para modificar/obsoletizar) -->
                                <div id="existing_document_section" class="space-y-2 hidden">
                                    <label for="document_search"
                                        class="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                        Documento a Modificar <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="text" id="document_search" placeholder="Buscar documento..."
                                            class="py-2 px-3 block w-full border border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400">
                                        <input type="hidden" id="existing_document_id" name="existing_document_id">
                                        <div id="search_results"
                                            class="absolute z-10 w-full mt-1 bg-white dark:bg-neutral-800 border border-gray-300 dark:border-neutral-700 rounded-lg shadow-lg hidden">
                                        </div>
                                    </div>
                                </div>

                                <!-- Tipo de Documento (Solo para crear nuevo) -->
                                <div id="document_type_section" class="space-y-2">
                                    <label for="document_type_id"
                                        class="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                        Tipo de Documento <span class="text-red-500">*</span>
                                    </label>
                                    <select id="document_type_id" name="document_type_id"
                                        class="py-2 px-3 block w-full border border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400">
                                        <option value="">Seleccione un tipo</option>
                                        @foreach ($documentTypes as $type)
                                            <option value="{{ $type->id }}"
                                                {{ old('document_type_id') == $type->id ? 'selected' : '' }}>
                                                {{ $type->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Nombre del Documento (Solo para crear nuevo) -->
                                <div id="document_name_section" class="space-y-2">
                                    <label for="document_name"
                                        class="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                        Nombre del Documento <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="document_name" name="document_name"
                                        value="{{ old('document_name') }}"
                                        class="py-2 px-3 block w-full border border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400">
                                </div>
                                <!-- Fecha de Creación -->
                                <div class="space-y-2">
                                    <label for="created_at"
                                        class="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                        Fecha de Vigencia <span class="text-red-500">*</span>
                                    </label>
                                    <input type="datetime-local" id="created_at" name="created_at"
                                        value="{{ old('created_at', date('Y-m-d\TH:i')) }}"
                                        class="py-2 px-3 block w-full border border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400"
                                        required>
                                </div>

                                <!-- Descripción -->
                                <div class="space-y-2 md:col-span-2">
                                    <label for="description"
                                        class="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                        Descripción del documento<span class="text-red-500">*</span>
                                    </label>
                                    <textarea id="description" name="description" rows="4"
                                        class="py-2 px-3 block w-full border border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400"
                                        required>{{ old('description') }}</textarea>
                                </div>
                            </div>

                            <!-- Documento -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                    Adjuntar Documento <span class="text-red-500">*</span>
                                </label>
                                <div class="mt-2">
                                    <div class="flex items-center justify-center w-full">
                                        <label for="document"
                                            class="flex flex-col items-center justify-center w-full h-28 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 dark:bg-neutral-900 dark:border-neutral-700 dark:hover:bg-neutral-800">
                                            <div class="flex items-center space-x-4 px-4">
                                                <svg class="w-8 h-8 text-gray-500 dark:text-neutral-400"
                                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                                </svg>
                                                <div class="text-center">
                                                    <p class="text-sm text-gray-500 dark:text-neutral-400">
                                                        <span class="font-semibold">Haga clic para cargar</span> o arrastre
                                                        y suelte
                                                    </p>
                                                    <p class="text-xs text-gray-500 dark:text-neutral-400 mt-1">
                                                        PDF, DOCX, DOC, XLS, XLSX (Máx. 100MB)
                                                    </p>
                                                </div>
                                            </div>
                                            <input id="document" name="document" type="file" class="hidden"
                                                accept=".pdf,.doc,.docx,.xls,.xlsx" required />
                                        </label>
                                    </div>
                                    <div id="file-selected" class="mt-2 text-sm text-gray-500 dark:text-neutral-400">
                                    </div>
                                </div>
                            </div>

                            <!-- Botones -->
                            <div class="flex justify-end space-x-4 mt-8">
                                <a href="{{ route('documents.requests.index') }}"
                                    class="py-2 px-4 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-gray-200 text-gray-500 hover:border-gray-300 hover:text-gray-600 dark:border-neutral-700 dark:text-neutral-400 dark:hover:text-neutral-300">
                                    Cancelar
                                </a>
                                <button type="submit"
                                    class="py-2 px-4 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600">
                                    <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" />
                                        <polyline points="17 21 17 13 7 13 7 21" />
                                        <polyline points="7 3 7 8 15 8" />
                                    </svg>
                                    Crear
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Elementos del DOM
                const form = document.getElementById('documentForm');
                const documentTypeSelect = document.getElementById('document_type_id');
                const documentNameInput = document.getElementById('document_name');
                const descriptionTextarea = document.getElementById('description');
                const process_id

                // Validación del formulario antes de enviar
                form.addEventListener('submit', function(e) {


                    if (!documentTypeSelect.value || !documentNameInput.value) {
                        e.preventDefault();
                        alert('Por favor complete todos los campos requeridos');
                        return;
                    }

                    // Validación de la descripción
                    if (!descriptionTextarea.value.trim()) {
                        e.preventDefault();
                        alert('Por favor ingrese una descripción');
                        return;
                    }
                });

                // Mostrar nombre del archivo seleccionado
                document.getElementById('document').addEventListener('change', function(e) {
                    const fileName = e.target.files[0]?.name;
                    document.getElementById('file-selected').textContent = fileName ?
                        `Archivo seleccionado: ${fileName}` : '';
                });

                // Ejecutar la función de cambio si hay un tipo de solicitud seleccionado inicialmente
                if (requestTypeSelect.value) {
                    requestTypeSelect.dispatchEvent(new Event('change'));
                }
            });
        </script>
    @endpush

@endsection
