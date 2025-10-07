@extends('layouts.app')

@section('title', 'Editar Solicitud de Documento')

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
                            Editar Documento #{{ $documentRequest->id }}
                        </h2>
                        <p class="mt-1 text-sm text-gray-600 dark:text-neutral-400">
                            Modifique los campos necesarios para actualizar el documento
                        </p>
                    </div>

                    <!-- Formulario -->
                    <form action="{{ route('documents.requests.update', $documentRequest->id) }}" 
                        method="POST" 
                        enctype="multipart/form-data" 
                        class="space-y-6"
                        id="editForm">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Proceso -->
                            <div class="space-y-2">
                                <label for="process_id" class="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                    Origen del Proceso <span class="text-red-500">*</span>
                                </label>
                                <select id="process_id" name="process_id"
                                    class="py-2 px-3 block w-full border border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400"
                                    required>
                                    <option value="">Seleccione un proceso</option>
                                    @foreach($processes as $process)
                                        <option value="{{ $process->id }}" {{ old('process_id', $documentRequest->process_id) == $process->id ? 'selected' : '' }}>
                                            {{ $process->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('process_id')
                                    <p class="text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Usuario Solicitante -->
                            <div class="space-y-2">
                                <label for="user_id" class="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                    Usuario Solicitante <span class="text-red-500">*</span>
                                </label>
                                <select id="user_id" name="user_id"
                                    class="py-2 px-3 block w-full border border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400"
                                    required>
                                    <option value="">Cargando líderes...</option>
                                </select>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    Los líderes del proceso aparecerán aquí automáticamente
                                </p>
                                @error('user_id')
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
                                        class="py-2 px-3 block w-full border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400" 
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
                                       class="py-2 px-3 block w-full border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400"
                                       required>
                                @error('document_name')
                                    <p class="text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Fecha de Vigencia -->
                            <div class="space-y-2">
                                <label for="created_at" class="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                    Fecha de Vigencia <span class="text-red-500">*</span>
                                </label>
                                <input type="datetime-local" 
                                    id="created_at" 
                                    name="created_at" 
                                    value="{{ old('created_at', $documentRequest->created_at ? $documentRequest->created_at->format('Y-m-d\TH:i') : date('Y-m-d\TH:i')) }}"
                                    class="py-2 px-3 block w-full border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400"
                                    required>
                                @error('created_at')
                                    <p class="text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Versión (solo lectura) -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                    Versión Actual
                                </label>
                                <input type="text" 
                                       value="v{{ $documentRequest->version }}"
                                       class="py-2 px-3 block w-full border-gray-200 rounded-lg text-sm bg-gray-100 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400"
                                       disabled>
                            </div>

                            <!-- Descripción del Documento -->
                            <div class="space-y-2 md:col-span-2">
                                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                    Descripción del Documento <span class="text-red-500">*</span>
                                </label>
                                <textarea id="description" 
                                          name="description" 
                                          rows="4"
                                          class="py-2 px-3 block w-full border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400"
                                          required>{{ old('description', $documentRequest->description) }}</textarea>
                                @error('description')
                                    <p class="text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Documento Actual -->
                        @if($documentRequest->final_document_path)
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                Documento Actual
                            </label>
                            <div class="flex items-center space-x-4 p-4 bg-gray-50 dark:bg-neutral-900 rounded-lg border border-gray-200 dark:border-neutral-700">
                                <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $documentRequest->document_name }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-neutral-400">
                                        Cargado el {{ $documentRequest->created_at->format('d/m/Y H:i') }}
                                    </p>
                                </div>
                                <div class="flex space-x-2">
                                    <a href="{{ route('documents.requests.preview-final', $documentRequest->id) }}" 
                                       target="_blank"
                                       class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-blue-700 bg-blue-100 rounded-lg hover:bg-blue-200 transition-colors">
                                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        Ver
                                    </a>
                                    <a href="{{ route('documents.requests.download-final', $documentRequest->id) }}" 
                                       class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                        </svg>
                                        Descargar
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Actualizar Documento -->
                        <div class="space-y-2">
                            <label for="document" class="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                {{ $documentRequest->final_document_path ? 'Reemplazar Documento' : 'Adjuntar Documento' }}
                            </label>
                            <div class="mt-2">
                                <div class="flex items-center justify-center w-full">
                                    <label for="document" class="flex flex-col items-center justify-center w-full h-28 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 dark:bg-neutral-900 dark:border-neutral-700 dark:hover:bg-neutral-800">
                                        <div class="flex items-center space-x-4 px-4">
                                            <svg class="w-8 h-8 text-gray-500 dark:text-neutral-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                            </svg>
                                            <div class="text-center">
                                                <p class="text-sm text-gray-500 dark:text-neutral-400">
                                                    <span class="font-semibold">Haga clic para cargar</span> o arrastre y suelte
                                                </p>
                                                <p class="text-xs text-gray-500 dark:text-neutral-400 mt-1">
                                                    PDF, DOCX, DOC, XLS, XLSX (Máx. 100MB)
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
                            <a href="{{ route('documents.published') }}" 
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
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('editForm');
        const processSelect = document.getElementById('process_id');
        const userSelect = document.getElementById('user_id');
        const documentInput = document.getElementById('document');
        const fileSelected = document.getElementById('file-selected');
        const documentNameInput = document.getElementById('document_name');
        const descriptionTextarea = document.getElementById('description');

        // Datos del documento actual
        const currentProcessId = '{{ $documentRequest->process_id }}';
        const currentUserId = '{{ $documentRequest->user_id }}';

        console.log('Proceso actual:', currentProcessId);
        console.log('Usuario actual:', currentUserId);

        // Función para cargar líderes del proceso
        function loadProcessLeaders(processId, selectUserId = null) {
            userSelect.innerHTML = '<option value="">Cargando líderes...</option>';
            userSelect.disabled = true;

            if (!processId) {
                userSelect.innerHTML = '<option value="">Primero seleccione un proceso</option>';
                return;
            }

            fetch(`{{ route('documents.requests.process-leaders') }}?process_id=${processId}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                console.log('Líderes recibidos:', data);
                
                if (data.success && data.leaders.length > 0) {
                    userSelect.innerHTML = '<option value="">Seleccione un líder</option>';
                    data.leaders.forEach(leader => {
                        const option = document.createElement('option');
                        option.value = leader.id;
                        option.textContent = `${leader.name} (${leader.role})`;
                        
                        // Seleccionar el usuario actual si coincide
                        if (selectUserId && leader.id == selectUserId) {
                            option.selected = true;
                        }
                        
                        userSelect.appendChild(option);
                    });
                    userSelect.disabled = false;
                } else {
                    userSelect.innerHTML = '<option value="">Este proceso no tiene líderes asignados</option>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                userSelect.innerHTML = '<option value="">Error al cargar líderes</option>';
            });
        }

        // Cargar líderes del proceso actual al iniciar
        if (currentProcessId) {
            loadProcessLeaders(currentProcessId, currentUserId);
        }

        // Recargar líderes cuando cambia el proceso
        processSelect.addEventListener('change', function() {
            loadProcessLeaders(this.value);
        });

        // Mostrar nombre del archivo seleccionado
        documentInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const size = (file.size / 1024 / 1024).toFixed(2);
                fileSelected.textContent = `Archivo seleccionado: ${file.name} (${size} MB)`;
            } else {
                fileSelected.textContent = '';
            }
        });

        // Validación del formulario
        form.addEventListener('submit', function(e) {
            if (!processSelect.value) {
                e.preventDefault();
                alert('Por favor seleccione un proceso');
                processSelect.focus();
                return;
            }

            if (!userSelect.value) {
                e.preventDefault();
                alert('Por favor seleccione un líder como solicitante');
                userSelect.focus();
                return;
            }

            if (!documentNameInput.value.trim()) {
                e.preventDefault();
                alert('Por favor ingrese el nombre del documento');
                documentNameInput.focus();
                return;
            }

            if (!descriptionTextarea.value.trim()) {
                e.preventDefault();
                alert('Por favor ingrese una descripción');
                descriptionTextarea.focus();
                return;
            }
        });
    });
</script>
@endpush

@endsection