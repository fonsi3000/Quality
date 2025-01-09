@extends('layouts.app')

@section('title', 'Procesos')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="bg-white rounded-xl shadow-sm dark:bg-neutral-800">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200 dark:border-neutral-700">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h2 class="text-xl font-bold text-gray-800 dark:text-neutral-200">
                        Gestión de Procesos
                    </h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-neutral-400">
                        Administra los procesos y sus líderes
                    </p>
                </div>
                <div>
                    <a href="{{ route('processes.create') }}" 
                       class="inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600 px-4 py-2">
                        <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 12h14"/>
                            <path d="M12 5v14"/>
                        </svg>
                        Agregar Nuevo Proceso
                    </a>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="p-6 border-b border-gray-200 dark:border-neutral-700">
            <form action="{{ route('processes.index') }}" method="GET" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Búsqueda -->
                    <div class="relative">
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}"
                               class="py-2 px-3 ps-11 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-400 dark:focus:ring-neutral-600"
                               placeholder="Buscar por nombre o líder...">
                        <div class="absolute inset-y-0 start-0 flex items-center pointer-events-none ps-4">
                            <svg class="size-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                            </svg>
                        </div>
                    </div>

                    <!-- Filtro por estado -->
                    <div>
                        <select name="status" 
                                class="py-2 px-3 pe-9 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-400">
                            <option value="">Todos los estados</option>
                            <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Activos</option>
                            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactivos</option>
                        </select>
                    </div>

                    <!-- Filtro por líder -->
                    <div>
                        <select name="leader" 
                                class="py-2 px-3 pe-9 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-400">
                            <option value="">Todos los líderes</option>
                            <option value="with_leader" {{ request('leader') === 'with_leader' ? 'selected' : '' }}>Con líder asignado</option>
                            <option value="without_leader" {{ request('leader') === 'without_leader' ? 'selected' : '' }}>Sin líder asignado</option>
                        </select>
                    </div>
                </div>

                <!-- Botones de filtro -->
                <div class="flex justify-end gap-x-2">
                    <a href="{{ route('processes.index') }}" 
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
            <div class="p-4 mb-4 mx-6 mt-6 text-sm border rounded-lg bg-green-100 text-green-800 border-green-200 dark:border-green-900 dark:bg-green-800/10 dark:text-green-400" role="alert">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 mb-4 mx-6 mt-6 text-sm border rounded-lg bg-red-100 text-red-800 border-red-200 dark:border-red-900 dark:bg-red-800/10 dark:text-red-400" role="alert">
                {{ session('error') }}
            </div>
        @endif

        <!-- Tabla -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                <thead class="bg-gray-50 dark:bg-neutral-700">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-start">
                            <a href="{{ route('processes.index', array_merge(request()->query(), [
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
                            <span class="text-sm text-gray-500 dark:text-neutral-400">Líder</span>
                        </th>
                        <th scope="col" class="px-6 py-3 text-start">
                            <span class="text-sm text-gray-500 dark:text-neutral-400">Estado</span>
                        </th>
                        <th scope="col" class="px-6 py-3 text-start">
                            <a href="{{ route('processes.index', array_merge(request()->query(), [
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
                        <th scope="col" class="px-6 py-3 text-end">
                            <span class="text-sm text-gray-500 dark:text-neutral-400">Acciones</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-neutral-700">
                    @forelse($processes as $process)
                        <tr class="hover:bg-gray-100 dark:hover:bg-neutral-700">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">
                                {{ $process->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-neutral-400">
                                @if($process->leader)
                                    <div class="flex items-center gap-x-2">
                                        <span class="bg-blue-100 text-blue-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">
                                            {{ $process->leader->name }}
                                        </span>
                                    </div>
                                @else
                                    <span class="text-yellow-500 dark:text-yellow-400">Sin líder asignado</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 py-0.5 px-2 rounded-full text-xs font-medium {{ $process->active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                    {{ $process->active ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-neutral-400">
                                {{ $process->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-end text-sm font-medium">
                                <div class="flex justify-end items-center gap-2">
                                    <!-- Botón para asignar líder -->
                                    <button type="button" 
                                            onclick="openAssignLeaderModal({{ $process->id }}, '{{ $process->leader ? $process->leader->id : '' }}')"
                                            class="text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300"
                                            title="Asignar Líder">
                                        <svg class="size-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                            <circle cx="9" cy="7" r="4"/>
                                            <path d="M19 8v6"/>
                                            <path d="M22 11h-6"/>
                                        </svg>
                                    </button>

                                    <!-- Botón editar -->
                                    <a href="{{ route('processes.edit', $process) }}" 
                                       class="text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300"
                                       title="Editar">
                                        <svg class="size-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/>
                                            <path d="m15 5 4 4"/>
                                        </svg>
                                    </a>
                                    <!-- Botón eliminar -->
                                    <form action="{{ route('processes.destroy', $process) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300"
                                                onclick="return confirm('¿Estás seguro de eliminar este proceso?')"
                                                title="Eliminar">
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
                            <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center dark:text-neutral-400">
                                No se encontraron procesos
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        @if($processes->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 dark:border-neutral-700">
                {{ $processes->links() }}
            </div>
        @endif
    </div>
</div>
<!-- Modal para asignar líder -->
<div id="assignLeaderModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-[480px] shadow-lg rounded-md bg-white dark:bg-neutral-800">
        <div class="mt-3">
            <div class="flex justify-between items-center pb-3">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                    Asignar Líder al Proceso
                </h3>
                <button onclick="closeAssignLeaderModal()" class="text-gray-400 hover:text-gray-500">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <form id="assignLeaderForm" method="POST" class="mt-4">
                @csrf
                @method('PUT')
                
                <!-- Campo de búsqueda -->
                <div class="mb-4">
                    <div class="relative">
                        <input type="text" 
                               id="searchUser" 
                               class="py-2 px-3 ps-9 block w-full border border-gray-300 rounded-md text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-300"
                               placeholder="Buscar usuario por nombre...">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3">
                            <svg class="size-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                            </svg>
                        </div>
                    </div>
                </div>
                
                <!-- Lista de usuarios -->
                <div class="max-h-64 overflow-y-auto border border-gray-200 rounded-md dark:border-neutral-700">
                    <div id="usersList" class="divide-y divide-gray-200 dark:divide-neutral-700">
                        @foreach($users as $user)
                            <label class="flex items-center px-4 py-2 hover:bg-gray-50 dark:hover:bg-neutral-700 cursor-pointer">
                                <input type="radio" 
                                       name="leader_id" 
                                       value="{{ $user->id }}"
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700">
                                <span class="ml-2 text-sm text-gray-900 dark:text-neutral-200">{{ $user->name }}</span>
                            </label>
                        @endforeach
                    </div>
                    <!-- Mensaje cuando no hay resultados -->
                    <div id="noResults" class="hidden p-4 text-sm text-gray-500 text-center dark:text-neutral-400">
                        No se encontraron usuarios
                    </div>
                </div>

                <div class="flex justify-end gap-2 mt-4">
                    <button type="button" 
                            onclick="closeAssignLeaderModal()"
                            class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 dark:bg-neutral-700 dark:text-neutral-200 dark:hover:bg-neutral-600">
                        Cancelar
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Asignar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Funciones existentes del modal...

function filterUsers(searchTerm) {
    const usersList = document.getElementById('usersList');
    const noResults = document.getElementById('noResults');
    const labels = usersList.getElementsByTagName('label');
    let hasResults = false;

    searchTerm = searchTerm.toLowerCase();

    for (const label of labels) {
        const userName = label.textContent.trim().toLowerCase();
        if (userName.includes(searchTerm)) {
            label.classList.remove('hidden');
            hasResults = true;
        } else {
            label.classList.add('hidden');
        }
    }

    // Mostrar/ocultar mensaje de no resultados
    noResults.classList.toggle('hidden', hasResults);
}

function openAssignLeaderModal(processId, currentLeaderId = '') {
    const modal = document.getElementById('assignLeaderModal');
    const form = document.getElementById('assignLeaderForm');
    const searchInput = document.getElementById('searchUser');
    
    form.action = `/processes/${processId}/assign-leader`;
    
    // Limpiar búsqueda y mostrar todos los usuarios
    searchInput.value = '';
    filterUsers('');
    
    // Seleccionar el líder actual si existe
    if (currentLeaderId) {
        const radio = form.querySelector(`input[value="${currentLeaderId}"]`);
        if (radio) radio.checked = true;
    }
    
    modal.classList.remove('hidden');
    searchInput.focus();
}

function closeAssignLeaderModal() {
    const modal = document.getElementById('assignLeaderModal');
    modal.classList.add('hidden');
}

// Inicializar la búsqueda cuando el documento esté listo
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchUser');
    
    searchInput.addEventListener('input', function(e) {
        filterUsers(e.target.value);
    });

    // Prevenir el envío del formulario al presionar enter en la búsqueda
    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
        }
    });
});

// Cerrar el modal al hacer clic fuera
window.onclick = function(event) {
    const modal = document.getElementById('assignLeaderModal');
    if (event.target == modal) {
        closeAssignLeaderModal();
    }
}
</script>
@endpush
@endsection