@extends('layouts.app')

@section('title', 'Editar Solicitud de Documento')

@section('content')
<div class="flex flex-col">
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-neutral-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-8">
                    <!-- Encabezado -->
                    <div class="mb-8">
                        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">
                            Editar Solicitud de Documento #{{ $documentRequest->id }}
                        </h2>
                        <p class="mt-1 text-sm text-gray-600 dark:text-neutral-400">
                            Modifique los campos necesarios para actualizar la solicitud
                        </p>
                    </div>

                    <!-- Formulario -->
                    <form action="{{ route('documents.requests.update', $documentRequest->id) }}" 
                        method="POST" 
                        enctype="multipart/form-data" 
                        class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Tipo de Solicitud -->
                            <div class="space-y-2">
                                <label for="request_type" class="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                    Tipo de Solicitud <span class="text-red-500">*</span>
                                </label>
                                <select id="request_type" 
                                        name="request_type" 
                                        class="py-2 px-3 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400" 
                                        required>
                                    <option value="">Seleccione un tipo</option>
                                    <option value="create" {{ old('request_type', $documentRequest->request_type) == 'create' ? 'selected' : '' }}>Crear Nuevo Documento</option>
                                    <option value="modify" {{ old('request_type', $documentRequest->request_type) == 'modify' ? 'selected' : '' }}>Modificar Documento Existente</option>
                                </select>
                                @error('request_type')
                                    <p class="text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Tipo de Documento -->
                            <div class="space-y-2">
                                <label for="document_type_id" class="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                    Tipo de Documento <span class="text-red-500">*</span>
                                </label>
                                <select id="document_type_id" 
                                        name="document_type_id" 
                                        class="py-2 px-3 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400" 
                                        required>
                                    <option value="">Seleccione un tipo</option>
                                    @foreach($documentTypes as $type)
                                        <option value="{{ $type->id }}" {{ old('document_type_id', $documentRequest->document_type_id) == $type->id ? 'selected' : '' }}>
                                            {{ $type->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('document_type_id')
                                    <p class="text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Nombre del Documento -->
                            <div class="space-y-2">
                                <label for="document_name" class="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                    Nombre del Documento <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       id="document_name" 
                                       name="document_name" 
                                       value="{{ old('document_name', $documentRequest->document_name) }}"
                                       class="py-2 px-3 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400"
                                       required>
                                @error('document_name')
                                    <p class="text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Descripción del Documento -->
                            <div class="space-y-2 md:col-span-2">
                                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                    Descripción del Documento <span class="text-red-500">*</span>
                                </label>
                                <textarea id="description" 
                                          name="description" 
                                          rows="4"
                                          class="py-2 px-3 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400"
                                          required>{{ old('description', $documentRequest->description) }}</textarea>
                                @error('description')
                                    <p class="text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Documento -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                Documento Actual
                            </label>
                            <div class="flex items-center space-x-2">
                                <p class="text-sm text-gray-500 dark:text-neutral-400">
                                    {{ $documentRequest->original_filename }}
                                </p>
                                <a href="{{ route('documents.requests.download', $documentRequest->id) }}" 
                                   class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                                    Descargar
                                </a>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label for="document" class="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                Actualizar Documento
                            </label>
                            <div class="mt-2">
                                <div class="flex items-center justify-center w-full">
                                    <label for="document" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 dark:bg-neutral-900 dark:border-neutral-700 dark:hover:bg-neutral-800">
                                        <div class="flex items-center space-x-4 px-4">
                                            <svg class="w-8 h-8 text-gray-500 dark:text-neutral-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                            </svg>
                                            <div class="text-center">
                                                <p class="text-sm text-gray-500 dark:text-neutral-400">
                                                    <span class="font-semibold">Haga clic para cargar</span> o arrastre y suelte
                                                </p>
                                                <p class="text-xs text-gray-500 dark:text-neutral-400 mt-1">
                                                    PDF, DOCX, DOC, XLS, XLSX
                                                </p>
                                            </div>
                                        </div>
                                        <input id="document" 
                                               name="document" 
                                               type="file" 
                                               class="hidden"
                                               accept=".pdf,.doc,.docx,.xls,.xlsx" />
                                    </label>
                                </div>
                                <div id="file-selected" class="mt-2 text-sm text-gray-500 dark:text-neutral-400"></div>
                                @error('document')
                                    <p class="text-sm text-red-600">{{ $message }}</p>
                                @enderror
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
                                <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                                    <polyline points="17 21 17 13 7 13 7 21"/>
                                    <polyline points="7 3 7 8 15 8"/>
                                </svg>
                                Guardar Cambios
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
    // Mostrar nombre del archivo seleccionado
    document.getElementById('document').addEventListener('change', function(e) {
        const fileName = e.target.files[0]?.name;
        const fileSize = e.target.files[0]?.size;
        const fileSelected = document.getElementById('file-selected');
        
        if (fileName) {
            const size = (fileSize / 1024 / 1024).toFixed(2); // Convertir a MB
            fileSelected.textContent = `Archivo seleccionado: ${fileName} (${size} MB)`;
        } else {
            fileSelected.textContent = '';
        }
    });
</script>
@endpush

@endsection