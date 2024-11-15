@extends('layouts.app')

@section('title', 'Unidades')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="bg-white rounded-xl shadow-sm dark:bg-neutral-800">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200 dark:border-neutral-700">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h2 class="text-xl font-bold text-gray-800 dark:text-neutral-200">
                        Gestión de Empresas
                    </h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-neutral-400">
                        Administra las Empresas del sistema
                    </p>
                </div>
                <div>
                    <a href="{{ route('units.create') }}" 
                       class="inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600 px-4 py-2">
                        Agregar Nueva Empresa
                    </a>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="p-6 border-b border-gray-200 dark:border-neutral-700">
            <form action="{{ route('units.index') }}" method="GET" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Búsqueda -->
                    <div class="relative">
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}"
                               class="py-2 px-3 ps-11 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-400 dark:focus:ring-neutral-600"
                               placeholder="Buscar unidades...">
                        <div class="absolute inset-y-0 start-0 flex items-center pointer-events-none ps-4">
                            <svg class="size-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                            </svg>
                        </div>
                    </div>

                    {{-- <!-- Estado -->
                    <div>
                        <select name="status" 
                                class="py-2 px-3 pe-9 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-400 dark:focus:ring-neutral-600">
                            <option value="">Todos los estados</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Activos</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactivos</option>
                        </select>
                    </div>

                    <!-- Ordenamiento -->
                    <div>
                        <select name="sort" 
                                class="py-2 px-3 pe-9 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-400 dark:focus:ring-neutral-600">
                            <option value="created_at" {{ request('sort') === 'created_at' ? 'selected' : '' }}>Fecha de creación</option>
                            <option value="name" {{ request('sort') === 'name' ? 'selected' : '' }}>Nombre</option>
                        </select>
                    </div> --}}
                </div>

                <!-- Botones de filtro -->
                <div class="flex justify-end gap-x-2">
                    <a href="{{ route('units.index') }}" 
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
                            <a href="{{ route('units.index', array_merge(request()->query(), [
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
                            <a href="{{ route('units.index', array_merge(request()->query(), [
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
                    @forelse($units as $unit)
                        <tr class="hover:bg-gray-100 dark:hover:bg-neutral-700">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">
                                {{ $unit->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 py-0.5 px-2 rounded-full text-xs font-medium {{ $unit->active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                    {{ $unit->active ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-neutral-400">
                                {{ $unit->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-end text-sm font-medium">
                                <div class="flex justify-end items-center gap-2">
                                    <a href="{{ route('units.edit', $unit) }}" 
                                       class="text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                                        <svg class="size-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/>
                                            <path d="m15 5 4 4"/>
                                        </svg>
                                    </a>

                                    <form action="{{ route('units.destroy', $unit) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300"
                                                onclick="return confirm('¿Estás seguro de eliminar esta unidad?')">
                                            <svg class="size-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 024 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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
                                No se encontraron unidades
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        @if($units->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 dark:border-neutral-700">
                {{ $units->links() }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    // Script para mantener los filtros en la URL durante la paginación
    document.addEventListener('DOMContentLoaded', function () {
        // Manejador para ordenamiento de columnas
        const sortableColumns = document.querySelectorAll('[data-sort]');
        sortableColumns.forEach(column => {
            column.addEventListener('click', function(e) {
                e.preventDefault();
                const sort = this.dataset.sort;
                const currentDirection = new URLSearchParams(window.location.search).get('direction');
                const newDirection = currentDirection === 'asc' ? 'desc' : 'asc';
                
                const searchParams = new URLSearchParams(window.location.search);
                searchParams.set('sort', sort);
                searchParams.set('direction', newDirection);
                
                window.location.search = searchParams.toString();
            });
        });

        // Manejador para limpiar filtros
        const clearFiltersButton = document.querySelector('[data-clear-filters]');
        if (clearFiltersButton) {
            clearFiltersButton.addEventListener('click', function(e) {
                e.preventDefault();
                window.location.href = '{{ route('units.index') }}';
            });
        }

        // Autosubmit del formulario cuando cambia un select
        const filterSelects = document.querySelectorAll('select[name="status"], select[name="sort"]');
        filterSelects.forEach(select => {
            select.addEventListener('change', function() {
                this.closest('form').submit();
            });
        });

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