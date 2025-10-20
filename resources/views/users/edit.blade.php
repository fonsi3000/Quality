@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="max-w-4xl px-4 py-10 sm:px-6 lg:px-8 mx-auto">
    <div class="bg-white rounded-xl shadow p-4 sm:p-7 dark:bg-neutral-800">
        <div class="mb-8">
            <h2 class="text-xl font-bold text-gray-800 dark:text-neutral-200">Editar usuario</h2>
            <p class="text-sm text-gray-600 dark:text-neutral-400 py-4">Actualice la información del usuario.</p>
        </div>

        <form action="{{ route('users.update', $user) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="grid sm:grid-cols-12 gap-2 sm:gap-6">
                <!-- Foto -->
                <div class="sm:col-span-3">
                    <label class="inline-block text-sm text-gray-800 mt-2.5 dark:text-neutral-200">Foto de perfil</label>
                </div>
                <div class="sm:col-span-9">
                    <div class="flex items-center gap-5">
                        <img id="preview-image" class="inline-block size-16 rounded-full ring-2 ring-white dark:ring-neutral-900" 
                             src="{{ $user->profile_photo ? asset('storage/' . $user->profile_photo) : asset('images/default-avatar.png') }}" 
                             alt="Avatar">
                        <input type="file" name="profile_photo" id="profile_photo"
                               class="block w-full text-sm text-gray-500 file:me-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700 file:disabled:opacity-50 file:disabled:pointer-events-none dark:file:bg-blue-500 dark:hover:file:bg-blue-400"
                               accept="image/*"
                               onchange="document.getElementById('preview-image').src = window.URL.createObjectURL(this.files[0])">
                    </div>
                    @error('profile_photo')
                        <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nombre -->
                <div class="sm:col-span-3">
                    <label for="name" class="inline-block text-sm text-gray-800 mt-2.5 dark:text-neutral-200">Nombre completo</label>
                </div>
                <div class="sm:col-span-9">
                    <input id="name" name="name" type="text"
                           class="py-2 px-3 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-400 dark:focus:ring-neutral-600 @error('name') border-red-500 @enderror"
                           value="{{ old('name', $user->name) }}" placeholder="Ingrese el nombre completo">
                    @error('name')
                        <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div class="sm:col-span-3">
                    <label for="email" class="inline-block text-sm text-gray-800 mt-2.5 dark:text-neutral-200">Email</label>
                </div>
                <div class="sm:col-span-9">
                    <input id="email" name="email" type="email"
                           class="py-2 px-3 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-400 dark:focus:ring-neutral-600 @error('email') border-red-500 @enderror"
                           value="{{ old('email', $user->email) }}" placeholder="correo@ejemplo.com">
                    @error('email')
                        <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Unidad -->
                <div class="sm:col-span-3">
                    <label for="unit_id" class="inline-block text-sm text-gray-800 mt-2.5 dark:text-neutral-200">Unidad</label>
                </div>
                <div class="sm:col-span-9">
                    <select id="unit_id" name="unit_id"
                            class="py-2 px-3 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-400 dark:focus:ring-neutral-600 @error('unit_id') border-red-500 @enderror">
                        <option value="">Seleccione una unidad</option>
                        @foreach($units as $unit)
                            <option value="{{ $unit->id }}" {{ old('unit_id', $user->unit_id) == $unit->id ? 'selected' : '' }}>{{ $unit->name }}</option>
                        @endforeach
                    </select>
                    @error('unit_id')
                        <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Proceso -->
                <div class="sm:col-span-3">
                    <label for="process_id" class="inline-block text-sm text-gray-800 mt-2.5 dark:text-neutral-200">Proceso</label>
                </div>
                <div class="sm:col-span-9">
                    <select id="process_id" name="process_id"
                            class="py-2 px-3 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-400 dark:focus:ring-neutral-600 @error('process_id') border-red-500 @enderror">
                        <option value="">Seleccione un proceso</option>
                        @foreach($processes as $process)
                            <option value="{{ $process->id }}" {{ old('process_id', $user->process_id) == $process->id ? 'selected' : '' }}>{{ $process->name }}</option>
                        @endforeach
                    </select>
                    @error('process_id')
                        <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Contenedor para procesos adicionales -->
            <div id="additional-processes-container" class="grid sm:grid-cols-12 gap-2 sm:gap-6 mt-2"></div>

            <!-- Botón para agregar más procesos -->
            <div class="grid sm:grid-cols-12 gap-2 sm:gap-6 mt-2">
                <div class="sm:col-span-3"></div>
                <div class="sm:col-span-9">
                    <button type="button" 
                            id="add-process-btn"
                            class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Agregar proceso adicional
                    </button>
                    <p class="text-xs text-gray-500 mt-2 dark:text-neutral-400">
                        Puede agregar hasta 4 procesos adicionales
                    </p>
                </div>
            </div>

            <!-- Grid para campos siguientes -->
            <div class="grid sm:grid-cols-12 gap-2 sm:gap-6 mt-2">
                <!-- Cargo -->
                <div class="sm:col-span-3">
                    <label for="position_id" class="inline-block text-sm text-gray-800 mt-2.5 dark:text-neutral-200">Cargo</label>
                </div>
                <div class="sm:col-span-9">
                    <select id="position_id" name="position_id"
                            class="py-2 px-3 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-400 dark:focus:ring-neutral-600 @error('position_id') border-red-500 @enderror">
                        <option value="">Seleccione un cargo</option>
                        @foreach($positions as $position)
                            <option value="{{ $position->id }}" {{ old('position_id', $user->position_id) == $position->id ? 'selected' : '' }}>{{ $position->name }}</option>
                        @endforeach
                    </select>
                    @error('position_id')
                        <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Rol -->
                <div class="sm:col-span-3">
                    <label for="role" class="inline-block text-sm text-gray-800 mt-2.5 dark:text-neutral-200">Rol del usuario</label>
                </div>
                <div class="sm:col-span-9">
                    <select id="role" name="role"
                            class="py-2 px-3 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-400 dark:focus:ring-neutral-600 @error('role') border-red-500 @enderror">
                        <option value="">Seleccione un rol</option>
                        <option value="admin" {{ old('role', $userRole?->name) == 'admin' ? 'selected' : '' }}>Administrador</option>
                        <option value="agent" {{ old('role', $userRole?->name) == 'agent' ? 'selected' : '' }}>Auditor</option>
                        <option value="user" {{ old('role', $userRole?->name) == 'user' ? 'selected' : '' }}>Colaborador</option>
                    </select>
                    @error('role')
                        <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                    @enderror
                    <p class="text-sm text-gray-500 mt-2 dark:text-neutral-400">El rol determina los permisos y accesos del usuario en el sistema</p>
                </div>

                <!-- Contraseña -->
                <div class="sm:col-span-3">
                    <label for="password" class="inline-block text-sm text-gray-800 mt-2.5 dark:text-neutral-200">Contraseña</label>
                </div>
                <div class="sm:col-span-9">
                    <input id="password" name="password" type="password"
                           class="py-2 px-3 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-400 dark:focus:ring-neutral-600 @error('password') border-red-500 @enderror"
                           placeholder="Dejar en blanco para mantener la contraseña actual">
                    @error('password')
                        <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirmar contraseña -->
                <div class="sm:col-span-3">
                    <label for="password_confirmation" class="inline-block text-sm text-gray-800 mt-2.5 dark:text-neutral-200">Confirmar Contraseña</label>
                </div>
                <div class="sm:col-span-9">
                    <input id="password_confirmation" name="password_confirmation" type="password"
                           class="py-2 px-3 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-400 dark:focus:ring-neutral-600"
                           placeholder="Dejar en blanco para mantener la contraseña actual">
                </div>

                <!-- Estado -->
                <div class="sm:col-span-3">
                    <label for="active" class="inline-block text-sm text-gray-800 mt-2.5 dark:text-neutral-200">Estado</label>
                </div>
                <div class="sm:col-span-9">
                    <label class="flex items-center">
                        <input type="checkbox" 
                               name="active" 
                               id="active" 
                               class="relative w-[3.25rem] h-7 bg-gray-200 checked:bg-none checked:bg-blue-600 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 ring-1 ring-transparent focus:border-blue-600 focus:ring-blue-600 ring-offset-white focus:outline-none appearance-none dark:bg-neutral-700 dark:checked:bg-blue-600 dark:focus:ring-offset-neutral-800

                                before:inline-block before:w-6 before:h-6 before:bg-white checked:before:bg-blue-200 before:translate-x-0 checked:before:translate-x-full before:shadow before:rounded-full before:transform before:ring-0 before:transition before:ease-in-out before:duration-200 dark:before:bg-neutral-400 dark:checked:before:bg-blue-200"
                               value="1"
                               {{ old('active', $user->active) ? 'checked' : '' }}>
                        <span class="ms-3 text-sm text-gray-600 dark:text-neutral-400">Usuario Activo</span>
                    </label>
                    @error('active')
                        <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Botones -->
            <div class="mt-5 flex justify-end gap-x-2">
                <a href="{{ route('users.index') }}"
                   class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 dark:bg-transparent dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-800">
                    Cancelar
                </a>
                <button type="submit"
                        class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600">
                    Actualizar
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const addBtn = document.getElementById('add-process-btn');
    const container = document.getElementById('additional-processes-container');
    const maxProcesses = 5;
    const maxAdditionalProcesses = 4; // Máximo de campos adicionales (2, 3, 4, 5)
    
    const processNames = {
        2: 'Segundo proceso',
        3: 'Tercer proceso',
        4: 'Cuarto proceso',
        5: 'Quinto proceso'
    };

    const processIds = {
        2: 'second_process_id',
        3: 'third_process_id',
        4: 'fourth_process_id',
        5: 'fifth_process_id'
    };

    // Valores actuales del usuario (pasados desde Blade)
    const userProcesses = {
        second_process_id: '{{ old("second_process_id", $user->second_process_id ?? "") }}',
        third_process_id: '{{ old("third_process_id", $user->third_process_id ?? "") }}',
        fourth_process_id: '{{ old("fourth_process_id", $user->fourth_process_id ?? "") }}',
        fifth_process_id: '{{ old("fifth_process_id", $user->fifth_process_id ?? "") }}'
    };

    // Cargar procesos existentes al cargar la página
    Object.keys(userProcesses).forEach((key, index) => {
        if (userProcesses[key]) {
            const processNum = index + 2; // 2, 3, 4, 5
            addProcessField(processNum, userProcesses[key]);
        }
    });

    function addProcessField(processNum, selectedValue = '') {
        const processId = processIds[processNum];
        const processLabel = processNames[processNum];

        const processHtml = `
            <div class="sm:col-span-3 process-field" data-process="${processNum}">
                <label for="${processId}" class="inline-block text-sm text-gray-800 mt-2.5 dark:text-neutral-200">
                    ${processLabel}
                </label>
            </div>
            
            <div class="sm:col-span-9 process-field" data-process="${processNum}">
                <div class="flex gap-2">
                    <select id="${processId}" name="${processId}" 
                            class="py-2 px-3 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-400 dark:focus:ring-neutral-600">
                        <option value="">Seleccione un proceso</option>
                        @foreach($processes as $process)
                            <option value="{{ $process->id }}" ${selectedValue == '{{ $process->id }}' ? 'selected' : ''}>{{ $process->name }}</option>
                        @endforeach
                    </select>
                    <button type="button" 
                            class="remove-process-btn py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-red-200 bg-white text-red-600 shadow-sm hover:bg-red-50 dark:bg-neutral-800 dark:border-red-700 dark:text-red-400 dark:hover:bg-red-900"
                            data-process="${processNum}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        `;

        container.insertAdjacentHTML('beforeend', processHtml);
        updateButtonVisibility();
        attachRemoveListener();
    }

    function attachRemoveListener() {
        document.querySelectorAll('.remove-process-btn').forEach(btn => {
            // Remover listeners anteriores clonando el elemento
            const newBtn = btn.cloneNode(true);
            btn.parentNode.replaceChild(newBtn, btn);
            
            newBtn.addEventListener('click', function() {
                const processNum = this.getAttribute('data-process');
                const processId = processIds[processNum];
                
                // Buscar si existe un campo hidden para este proceso
                let hiddenInput = document.querySelector(`input[type="hidden"][name="${processId}"]`);
                
                // Si no existe, crearlo y agregarlo al formulario
                if (!hiddenInput) {
                    hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = processId;
                    hiddenInput.value = '';
                    document.querySelector('form').appendChild(hiddenInput);
                }
                
                // Asegurar que el valor esté vacío
                hiddenInput.value = '';
                
                // Remover los campos visibles
                const fieldsToRemove = document.querySelectorAll(`[data-process="${processNum}"]`);
                fieldsToRemove.forEach(field => field.remove());
                
                updateButtonVisibility();
            });
        });
    }

    function updateButtonVisibility() {
        // Contar cuántos campos de proceso adicionales están visibles
        const visibleProcesses = new Set();
        document.querySelectorAll('.process-field[data-process]').forEach(field => {
            visibleProcesses.add(field.getAttribute('data-process'));
        });
        
        const visibleCount = visibleProcesses.size;
        
        // Ocultar el botón si ya hay 4 procesos adicionales
        if (visibleCount >= maxAdditionalProcesses) {
            addBtn.style.display = 'none';
        } else {
            addBtn.style.display = 'inline-flex';
        }
    }

    addBtn.addEventListener('click', function() {
        // Contar procesos actuales
        const existingProcesses = new Set();
        document.querySelectorAll('.process-field[data-process]').forEach(field => {
            existingProcesses.add(parseInt(field.getAttribute('data-process')));
        });
        
        if (existingProcesses.size < maxAdditionalProcesses) {
            // Encontrar el siguiente número de proceso disponible
            let nextProcess = 2;
            while (nextProcess <= maxProcesses) {
                if (!existingProcesses.has(nextProcess)) {
                    addProcessField(nextProcess);
                    break;
                }
                nextProcess++;
            }
        }
    });
    
    // Inicializar la visibilidad del botón al cargar
    updateButtonVisibility();
});
</script>
@endsection