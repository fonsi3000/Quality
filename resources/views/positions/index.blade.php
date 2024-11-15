@extends('layouts.app')

@section('title', 'Cargos')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="bg-white rounded-xl shadow-sm dark:bg-neutral-800">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200 dark:border-neutral-700">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h2 class="text-xl font-bold text-gray-800 dark:text-neutral-200">
                        Gestión de Cargos
                    </h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-neutral-400">
                        Administra los Cargos del sistema
                    </p>
                </div>
                <div>
                    <a href="{{ route('positions.create') }}" 
                       class="inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600 px-4 py-2">
                        Agregar Nuevo Cargo
                    </a>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="p-6 border-b border-gray-200 dark:border-neutral-700">
            <form action="{{ route('positions.index') }}" method="GET" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Búsqueda -->
                    <div class="relative">
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}"
                               class="py-2 px-3 ps-11 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-400 dark:focus:ring-neutral-600"
                               placeholder="Buscar cargos...">
                        <div class="absolute inset-y-0 start-0 flex items-center pointer-events-none ps-4">
                            <svg class="size-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Botones de filtro -->
                <div class="flex justify-end gap-x-2">
                    <a href="{{ route('positions.index') }}" 
                       class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-transparent dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-800">
                        Limpiar
                    </a>
                    <button type="submit" 
                            class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600">
                        Aplicar Filtros
                    </button>
                </div>
            </form>
        </div>

        <!-- Mensajes -->
        @if(session('success'))
            <div class="p-4 mb-4 mx-6 mt-6 bg-green-100 border border-green-400 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 mb-4 mx-6 mt-6 bg-red-100 border border-red-400 text-red-700 rounded">
                {{ session('error') }}
            </div>
        @endif

        <!-- Tabla -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                <thead class="bg-gray-50 dark:bg-neutral-700">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-start">
                            <a href="{{ route('positions.index', array_merge(request()->query(), [
                                'sort' => 'name',
                                'direction' => request('sort') === 'name' && request('direction') === 'asc' ? 'desc' : 'asc'
                            ])) }}" class="group inline-flex items-center gap-x-2 text-sm text-gray-500 dark:text-neutral-400">
                                Nombre
                                <svg class="flex-shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    @if(request('sort') === 'name')
                                        @if(request('direction') === 'asc')
                                            <path d="m18 15-6-6-6 6"/>
                                        @else
                                            <path d="m6 9 6 6 6-6"/>
                                        @endif
                                    @else
                                        <path d="m18 15-6-6-6 6"/>
                                    @endif
                                </svg>
                            </a>
                        </th>
                        <th scope="col" class="px-6 py-3 text-start">
                            <span class="text-sm text-gray-500 dark:text-neutral-400">Estado</span>
                        </th>
                        <th scope="col" class="px-6 py-3 text-start">
                            <a href="{{ route('positions.index', array_merge(request()->query(), [
                                'sort' => 'created_at',
                                'direction' => request('sort') === 'created_at' && request('direction') === 'asc' ? 'desc' : 'asc'
                            ])) }}" class="group inline-flex items-center gap-x-2 text-sm text-gray-500 dark:text-neutral-400">
                                Fecha de Creación
                                <svg class="flex-shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    @if(request('sort') === 'created_at')
                                        @if(request('direction') === 'asc')
                                            <path d="m18 15-6-6-6 6"/>
                                        @else
                                            <path d="m6 9 6 6 6-6"/>
                                        @endif
                                    @else
                                        <path d="m18 15-6-6-6 6"/>
                                    @endif
                                </svg>
                            </a>
                        </th>
                        <th scope="col" class="px-6 py-3 text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-neutral-700">
                    @forelse($positions as $position)
                        <tr class="hover:bg-gray-100 dark:hover:bg-neutral-700">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">
                                {{ $position->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 py-0.5 px-2 rounded-full text-xs font-medium {{ $position->active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                    {{ $position->active ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-neutral-400">
                                {{ $position->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-end text-sm font-medium">
                                <div class="flex justify-end items-center gap-2">
                                    <a href="{{ route('positions.edit', $position) }}" 
                                       class="text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                                        <svg class="size-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/>
                                            <path d="m15 5 4 4"/>
                                        </svg>
                                    </a>

                                    <form action="{{ route('positions.destroy', $position) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300"
                                                onclick="return confirm('¿Estás seguro de eliminar este cargo?')">
                                            <svg class="size-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M3 6h18"/>
                                                <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/>
                                                <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>
                                                <line x1="10" x2="10" y1="11" y2="17"/>
                                                <line x1="14" x2="14" y1="11" y2="17"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center dark:text-neutral-400">
                                No se encontraron cargos
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        @if($positions->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 dark:border-neutral-700">
                {{ $positions->links() }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Debounce para la búsqueda
        let searchTimeout;
        const searchInput = document.querySelector('input[name="search"]');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    this.closest('form').submit();
                }, 500);
            });
        }
    });
</script>
@endpush
@endsection