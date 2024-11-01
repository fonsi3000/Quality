@extends('layouts.app')

@section('title', 'Tipos de Documentos')

@section('content')
<div class="max-w-4xl px-4 py-10 sm:px-6 lg:px-8 mx-auto">
    <div class="bg-white rounded-xl shadow p-4 sm:p-7 dark:bg-neutral-800">
        <div class="mb-8">
            <h2 class="text-xl font-bold text-gray-800 dark:text-neutral-200">
                Crear nuevo tipo de documento
            </h2>
            <p class="text-sm text-gray-600 dark:text-neutral-400 py-4">
                Complete la información para crear un nuevo tipo de documento.
            </p>
        </div>

        <form action="{{ route('document-types.store') }}" method="POST">
            @csrf
            <!-- Grid -->
            <div class="grid sm:grid-cols-12 gap-2 sm:gap-6">
                <div class="sm:col-span-3">
                    <label for="name" class="inline-block text-sm text-gray-800 mt-2.5 dark:text-neutral-200">
                        Nombre
                    </label>
                </div>

                <div class="sm:col-span-9">
                    <input id="name" name="name" type="text" 
                           class="py-2 px-3 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-400 dark:focus:ring-neutral-600 @error('name') border-red-500 @enderror"
                           value="{{ old('name') }}" 
                           placeholder="Ingrese el nombre del tipo de documento">
                    @error('name')
                        <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-3">
                    <label for="description" class="inline-block text-sm text-gray-800 mt-2.5 dark:text-neutral-200">
                        Descripción
                    </label>
                </div>

                <div class="sm:col-span-9">
                    <textarea id="description" name="description" rows="3"
                              class="py-2 px-3 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-400 dark:focus:ring-neutral-600 @error('description') border-red-500 @enderror"
                              placeholder="Ingrese una descripción del tipo de documento">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-3">
                    <div class="inline-block text-sm text-gray-800 mt-2.5 dark:text-neutral-200">
                        Estado
                    </div>
                </div>

                <div class="sm:col-span-9">
                    <div class="relative flex items-start">
                        <div class="flex items-center h-5">
                            <input type="checkbox" 
                                   id="is_active" 
                                   name="is_active" 
                                   class="border-gray-200 rounded text-blue-600 focus:ring-blue-500 dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800"
                                   value="1"
                                   {{ old('is_active', true) ? 'checked' : '' }}>
                        </div>
                        <label for="is_active" class="ms-3">
                            <span class="block text-sm text-gray-600 dark:text-neutral-400">Activo</span>
                        </label>
                    </div>
                    @error('is_active')
                        <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            <!-- End Grid -->

            <div class="mt-5 flex justify-end gap-x-2">
                <a href="{{ route('document-types.index') }}" 
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