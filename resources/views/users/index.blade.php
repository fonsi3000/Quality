@extends('layouts.app')

@section('title', 'Usuarios')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="bg-white rounded-lg shadow-sm dark:bg-neutral-800">
        <!-- Header más compacto -->
        <div class="px-4 py-3 border-b border-gray-200 dark:border-neutral-700">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-neutral-200">
                        Gestión de Usuarios
                    </h2>
                    <p class="mt-1 text-xs text-gray-600 dark:text-neutral-400">
                        Administra los usuarios del sistema
                    </p>
                </div>
                <div>
                    <a href="{{ route('users.create') }}" 
                       class="inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 px-3 py-1.5">
                        <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <line x1="19" x2="19" y1="8" y2="14"/>
                            <line x1="22" x2="16" y1="11" y2="11"/>
                        </svg>
                        Nuevo Usuario
                    </a>
                </div>
            </div>
        </div>

        <!-- Filtros más compactos -->
        <div class="p-4 border-b border-gray-200 dark:border-neutral-700">
            <form action="{{ route('users.index') }}" method="GET" class="space-y-3">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    <!-- Búsqueda -->
                    <div class="relative">
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}"
                               class="py-1.5 px-3 ps-9 block w-full border-gray-200 rounded-md text-sm"
                               placeholder="Buscar usuarios...">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                            <svg class="size-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                            </svg>
                        </div>
                    </div>

                    <!-- Selectores compactos -->
                    <div>
                        <select name="status" class="py-1.5 px-3 block w-full border-gray-200 rounded-md text-sm">
                            <option value="">Todos los estados</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Activos</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactivos</option>
                        </select>
                    </div>

                    <div>
                        <select name="role" class="py-1.5 px-3 block w-full border-gray-200 rounded-md text-sm">
                            <option value="">Todos los roles</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}" {{ request('role') === $role->name ? 'selected' : '' }}>
                                    {{ $role->display_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <select name="unit" class="py-1.5 px-3 block w-full border-gray-200 rounded-md text-sm">
                            <option value="">Todas las unidades</option>
                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}" {{ request('unit') == $unit->id ? 'selected' : '' }}>
                                    {{ $unit->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Botones de filtro más compactos -->
                <div class="flex justify-end gap-x-2">
                    <a href="{{ route('users.index') }}" 
                       class="py-1.5 px-3 text-sm font-medium rounded-md border border-gray-200 text-gray-600 hover:bg-gray-50">
                        Limpiar
                    </a>
                    <button type="submit" 
                            class="py-1.5 px-3 text-sm font-medium rounded-md bg-blue-600 text-white hover:bg-blue-700">
                        Aplicar
                    </button>
                </div>
            </form>
        </div>

        <!-- Mensajes de alerta más compactos -->
        @if(session('success'))
            <div class="mx-4 mt-3 p-2 text-sm bg-green-50 border border-green-200 text-green-600 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mx-4 mt-3 p-2 text-sm bg-red-50 border border-red-200 text-red-600 rounded">
                {{ session('error') }}
            </div>
        @endif

        <!-- Tabla mejorada -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-neutral-700">
                    <tr>
                        <th scope="col" class="px-4 py-2 text-start font-medium text-gray-500">
                            <a href="{{ route('users.index', array_merge(request()->query(), [
                                'sort' => 'name',
                                'direction' => request('sort') === 'name' && request('direction') === 'asc' ? 'desc' : 'asc'
                            ])) }}" class="group inline-flex items-center gap-x-1">
                                Nombre
                                <svg class="size-3" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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
                        <th scope="col" class="px-4 py-2 text-start font-medium text-gray-500">Email</th>
                        <th scope="col" class="px-4 py-2 text-start font-medium text-gray-500">Unidad</th>
                        <th scope="col" class="px-4 py-2 text-start font-medium text-gray-500">Rol</th>
                        <th scope="col" class="px-4 py-2 text-start font-medium text-gray-500">Estado</th>
                        <th scope="col" class="px-4 py-2 text-end font-medium text-gray-500">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-neutral-700">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50 dark:hover:bg-neutral-700">
                            <td class="px-4 py-2">
                                <div class="flex items-center gap-3">
                                    <img class="size-8 rounded-full" 
                                         src="{{ $user->profile_photo ? Storage::url($user->profile_photo) : asset('images/default-avatar.png') }}" 
                                         alt="{{ $user->name }}">
                                    <span class="font-medium text-gray-900 dark:text-neutral-200">
                                        {{ $user->name }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-4 py-2 text-gray-500">{{ $user->email }}</td>
                            <td class="px-4 py-2 text-gray-500">{{ $user->unit?->name ?? 'N/A' }}</td>
                            <td class="px-4 py-2">
                                @foreach($user->roles as $role)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700">
                                        @switch($role->name)
                                            @case('admin')
                                                Administrador
                                                @break
                                            @case('agent')
                                                Auditor
                                                @break
                                            @case('user')
                                                Colaborador
                                                @break
                                            @default
                                                {{ $role->name }}
                                        @endswitch
                                    </span>
                                @endforeach
                            </td>
                            <td class="px-4 py-2">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium {{ $user->active ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700' }}">
                                    <span class="size-1.5 rounded-full {{ $user->active ? 'bg-green-500' : 'bg-red-500' }}"></span>
                                    {{ $user->active ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="px-4 py-2">
                                <div class="flex justify-end items-center gap-1">
                                    <button type="button" 
                                            data-hs-overlay="#user-modal-{{ $user->id }}"
                                            class="p-1 hover:bg-gray-100 rounded-full">
                                        <svg class="size-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                                            <circle cx="12" cy="12" r="3"/>
                                        </svg>
                                    </button>

                                    <a href="{{ route('users.edit', $user) }}" 
                                       class="p-1 hover:bg-gray-100 rounded-full">
                                        <svg class="size-4 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/>
                                        </svg>
                                    </a>

                                    <form action="{{ route('users.toggle-active', $user) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" 
                                                class="p-1 hover:bg-gray-100 rounded-full"
                                                onclick="return confirm('¿Estás seguro?')">
                                            @if($user->active)
                                                <svg class="size-4 text-red-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <rect width="18" height="18" x="3" y="3" rx="2"/>
                                                    <path d="M15 9h-6v6h6V9Z"/>
                                                </svg>
                                            @else
                                                <svg class="size-4 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <rect width="18" height="18" x="3" y="3" rx="2"/>
                                                    <path d="M9 12h6"/>
                                                </svg>
                                            @endif
                                        </button>
                                    </form>

                                    @if(auth()->id() !== $user->id)
                                        <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="p-1 hover:bg-gray-100 rounded-full"
                                                    onclick="return confirm('¿Estás seguro?')">
                                                <svg class="size-4 text-red-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path d="M3 6h18"/>
                                                    <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/>
                                                    <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>
                                                    <line x1="10" x2="10" y1="11" y2="17"/>
                                                    <line x1="14" x2="14" y1="11" y2="17"/>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                No se encontraron usuarios
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación más compacta -->
        <div class="px-4 py-3 border-t border-gray-200 dark:border-neutral-700">
            {{ $users->links() }}
        </div>
    </div>
</div>

<!-- Modales de usuario mejorados -->
@foreach($users as $user)
<div id="user-modal-{{ $user->id }}" class="hs-overlay hidden w-full h-full fixed top-0 start-0 z-[60] overflow-x-hidden overflow-y-auto">
    <div class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all sm:max-w-lg sm:w-full m-3 sm:mx-auto">
        <div class="bg-white border shadow-sm rounded-xl dark:bg-neutral-800 dark:border-neutral-700">
            <div class="p-4 sm:p-8 overflow-y-auto">
                <!-- Encabezado del modal -->
                <div class="mb-6 text-center">
                    <div class="mb-4">
                        <img class="size-16 rounded-full mx-auto" 
                             src="{{ $user->profile_photo ? Storage::url($user->profile_photo) : asset('images/default-avatar.png') }}" 
                             alt="{{ $user->name }}">
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-neutral-200">
                        {{ $user->name }}
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-neutral-400">
                        {{ $user->email }}
                    </p>
                </div>

                <!-- Información del usuario -->
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <h5 class="text-xs font-medium text-gray-500 uppercase tracking-wider">Unidad</h5>
                            <p class="mt-1 text-sm text-gray-800 dark:text-neutral-200">
                                {{ $user->unit?->name ?? 'No asignada' }}
                            </p>
                        </div>
                        <div>
                            <h5 class="text-xs font-medium text-gray-500 uppercase tracking-wider">Proceso</h5>
                            <p class="mt-1 text-sm text-gray-800 dark:text-neutral-200">
                                {{ $user->process?->name ?? 'No asignado' }}
                            </p>
                        </div>
                        <div>
                            <h5 class="text-xs font-medium text-gray-500 uppercase tracking-wider">Segundo Proceso</h5>
                            <p class="mt-1 text-sm text-gray-800 dark:text-neutral-200">
                                {{ $user->secondaryProcess?->name ?? 'No asignado' }}
                            </p>
                        </div>
                    </div>

                    <div>
                        <h5 class="text-xs font-medium text-gray-500 uppercase tracking-wider">Roles</h5>
                        <div class="mt-1.5 flex flex-wrap gap-1.5">
                            @foreach($user->roles as $role)
                                <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-blue-50 text-blue-700 dark:bg-blue-900 dark:text-blue-200">
                                    @switch($role->name)
                                        @case('admin')
                                            Administrador
                                            @break
                                        @case('agent')
                                            Auditor
                                            @break
                                        @case('user')
                                            Colaborador
                                            @break
                                        @default
                                            {{ $role->name }}
                                    @endswitch
                                </span>
                            @endforeach
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <h5 class="text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</h5>
                            <span class="mt-1.5 inline-flex items-center gap-1 px-2 py-1 rounded-md text-xs font-medium {{ $user->active ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700' }}">
                                <span class="size-1.5 rounded-full {{ $user->active ? 'bg-green-500' : 'bg-red-500' }}"></span>
                                {{ $user->active ? 'Activo' : 'Inactivo' }}
                            </span>
                        </div>
                        <div>
                            <h5 class="text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha de Registro</h5>
                            <p class="mt-1 text-sm text-gray-800 dark:text-neutral-200">
                                {{ $user->created_at ? $user->created_at->format('d/m/Y') : 'No disponible' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer del modal -->
            <div class="flex justify-end items-center gap-x-2 py-3 px-4 border-t dark:border-neutral-700">
                <button type="button" 
                        class="py-1.5 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:text-white dark:hover:bg-neutral-700"
                        data-hs-overlay="#user-modal-{{ $user->id }}">
                    Cerrar
                </button>
                <a href="{{ route('users.edit', $user) }}" 
                   class="py-1.5 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700">
                    Editar Usuario
                </a>
            </div>
        </div>
    </div>
</div>
@endforeach

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
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

        document.querySelector('[data-clear-filters]')?.addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = '{{ route('users.index') }}';
        });
    });
</script>
@endpush

@endsection