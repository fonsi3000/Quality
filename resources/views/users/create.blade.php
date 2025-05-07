@extends('layouts.app')

@section('title', 'Crear un nuevo Usuario')

@section('content')
<div class="max-w-4xl px-4 py-10 sm:px-6 lg:px-8 mx-auto">
    <div class="bg-white rounded-xl shadow p-4 sm:p-7 dark:bg-neutral-800">
        <div class="mb-8">
            <h2 class="text-xl font-bold text-gray-800 dark:text-neutral-200">
                Crear un nuevo usuario
            </h2>
            <p class="text-sm text-gray-600 dark:text-neutral-400 py-4">
                Complete la información para crear un nuevo usuario.
            </p>
        </div>

        <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <!-- Grid -->
            <div class="grid sm:grid-cols-12 gap-2 sm:gap-6">
                <div class="sm:col-span-3">
                    <label class="inline-block text-sm text-gray-800 mt-2.5 dark:text-neutral-200">
                        Foto de perfil
                    </label>
                </div>

                <div class="sm:col-span-9">
                    <div class="flex items-center gap-5">
                        <img id="preview-image" class="inline-block size-16 rounded-full ring-2 ring-white dark:ring-neutral-900" 
                             src="{{ asset('images/default-avatar.png') }}" alt="Avatar">
                        <div class="flex gap-x-2">
                            <div>
                                <input type="file" name="profile_photo" id="profile_photo" 
                                       class="block w-full text-sm text-gray-500 file:me-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700 file:disabled:opacity-50 file:disabled:pointer-events-none dark:file:bg-blue-500 dark:hover:file:bg-blue-400"
                                       accept="image/*"
                                       onchange="document.getElementById('preview-image').src = window.URL.createObjectURL(this.files[0])">
                            </div>
                        </div>
                    </div>
                    @error('profile_photo')
                        <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-3">
                    <label for="name" class="inline-block text-sm text-gray-800 mt-2.5 dark:text-neutral-200">
                        Nombre completo
                    </label>
                </div>

                <div class="sm:col-span-9">
                    <input id="name" name="name" type="text" 
                           class="py-2 px-3 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-400 dark:focus:ring-neutral-600 @error('name') border-red-500 @enderror"
                           value="{{ old('name') }}" 
                           placeholder="Ingrese el nombre completo">
                    @error('name')
                        <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-3">
                    <label for="email" class="inline-block text-sm text-gray-800 mt-2.5 dark:text-neutral-200">
                        Email
                    </label>
                </div>

                <div class="sm:col-span-9">
                    <input id="email" name="email" type="email" 
                           class="py-2 px-3 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-400 dark:focus:ring-neutral-600 @error('email') border-red-500 @enderror"
                           value="{{ old('email') }}" 
                           placeholder="correo@ejemplo.com">
                    @error('email')
                        <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-3">
                    <label for="unit_id" class="inline-block text-sm text-gray-800 mt-2.5 dark:text-neutral-200">
                        Empresa
                    </label>
                </div>

                <div class="sm:col-span-9">
                    <select id="unit_id" name="unit_id" 
                            class="py-2 px-3 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-400 dark:focus:ring-neutral-600 @error('unit_id') border-red-500 @enderror">
                        <option value="">Seleccione una Empresa</option>
                        @foreach($units as $unit)
                            <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>
                                {{ $unit->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('unit_id')
                        <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-3">
                    <label for="process_id" class="inline-block text-sm text-gray-800 mt-2.5 dark:text-neutral-200">
                        Proceso
                    </label>
                </div>

                <div class="sm:col-span-9">
                    <select id="process_id" name="process_id" 
                            class="py-2 px-3 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-400 dark:focus:ring-neutral-600 @error('process_id') border-red-500 @enderror">
                        <option value="">Seleccione un proceso</option>
                        @foreach($processes as $process)
                            <option value="{{ $process->id }}" {{ old('process_id') == $process->id ? 'selected' : '' }}>
                                {{ $process->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('process_id')
                        <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-3">
                    <label for="second_process_id" class="inline-block text-sm text-gray-800 mt-2.5 dark:text-neutral-200">
                        Segundo proceso (opcional)
                    </label>
                </div>
                
                <div class="sm:col-span-9">
                    <select id="second_process_id" name="second_process_id" 
                            class="py-2 px-3 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-400 dark:focus:ring-neutral-600 @error('second_process_id') border-red-500 @enderror">
                        <option value="">Seleccione un segundo proceso</option>
                        @foreach($processes as $process)
                            <option value="{{ $process->id }}" {{ old('second_process_id') == $process->id ? 'selected' : '' }}>
                                {{ $process->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('second_process_id')
                        <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-3">
                    <label for="position_id" class="inline-block text-sm text-gray-800 mt-2.5 dark:text-neutral-200">
                        Cargo
                    </label>
                </div>

                <div class="sm:col-span-9">
                    <select id="position_id" name="position_id" 
                            class="py-2 px-3 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-400 dark:focus:ring-neutral-600 @error('position_id') border-red-500 @enderror">
                        <option value="">Seleccione un cargo</option>
                        @foreach($positions as $position)
                            <option value="{{ $position->id }}" {{ old('position_id') == $position->id ? 'selected' : '' }}>
                                {{ $position->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('position_id')
                        <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-3">
                    <label for="role" class="inline-block text-sm text-gray-800 mt-2.5 dark:text-neutral-200">
                        Rol del usuario
                    </label>
                </div>

                <div class="sm:col-span-9">
                    <select id="role" name="role" 
                            class="py-2 px-3 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-400 dark:focus:ring-neutral-600 @error('role') border-red-500 @enderror">
                        <option value="">Seleccione un rol</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Administrador</option>
                        <option value="agent" {{ old('role') == 'agent' ? 'selected' : '' }}>Auditor</option>
                        <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>Colaborador</option>
                    </select>
                    @error('role')
                        <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                    @enderror
                    <p class="text-sm text-gray-500 mt-2 dark:text-neutral-400">
                        El rol determina los permisos y accesos del usuario en el sistema
                    </p>
                </div>

                <div class="sm:col-span-3">
                    <label for="password" class="inline-block text-sm text-gray-800 mt-2.5 dark:text-neutral-200">
                        Contraseña
                    </label>
                </div>

                <div class="sm:col-span-9">
                    <input id="password" name="password" type="password" 
                           class="py-2 px-3 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-400 dark:focus:ring-neutral-600 @error('password') border-red-500 @enderror"
                           placeholder="********">
                    @error('password')
                        <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-3">
                    <label for="password_confirmation" class="inline-block text-sm text-gray-800 mt-2.5 dark:text-neutral-200">
                        Confirmar Contraseña
                    </label>
                </div>

                <div class="sm:col-span-9">
                    <input id="password_confirmation" name="password_confirmation" type="password" 
                           class="py-2 px-3 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-400 dark:focus:ring-neutral-600"
                           placeholder="********">
                </div>

                <div class="sm:col-span-3">
                    <label for="active" class="inline-block text-sm text-gray-800 mt-2.5 dark:text-neutral-200">
                        Estado
                    </label>
                </div>

                <div class="sm:col-span-9">
                    <label class="flex items-center">
                        <input type="checkbox" 
                               name="active" 
                               id="active" 
                               class="relative w-[3.25rem] h-7 bg-gray-200 checked:bg-none checked:bg-blue-600 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 ring-1 ring-transparent focus:border-blue-600 focus:ring-blue-600 ring-offset-white focus:outline-none appearance-none dark:bg-neutral-700 dark:checked:bg-blue-600 dark:focus:ring-offset-neutral-800

                                before:inline-block before:w-6 before:h-6 before:bg-white checked:before:bg-blue-200 before:translate-x-0 checked:before:translate-x-full before:shadow before:rounded-full before:transform before:ring-0 before:transition before:ease-in-out before:duration-200 dark:before:bg-neutral-400 dark:checked:before:bg-blue-200"
                               value="1"
                               {{ old('active', true) ? 'checked' : '' }}>
                        <span class="ms-3 text-sm text-gray-600 dark:text-neutral-400">Usuario Activo</span>
                    </label>
                    @error('active')
                        <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            <!-- End Grid -->

            <div class="mt-5 flex justify-end gap-x-2">
                <a href="{{ route('users.index') }}" 
                   class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-transparent dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-800">
                    Cancelar
                </a>
                <button type="submit" 
                        class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600">
                    Guardar
                </button>
            </div>
        </form>
    </div>
</div>
@endsection