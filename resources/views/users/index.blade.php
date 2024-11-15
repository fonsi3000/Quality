@extends('layouts.app')

@section('title', 'Usuarios')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="bg-white rounded-xl shadow-sm dark:bg-neutral-800">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200 dark:border-neutral-700">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h2 class="text-xl font-bold text-gray-800 dark:text-neutral-200">
                        Gestión de Usuarios
                    </h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-neutral-400">
                        Administra los usuarios del sistema
                    </p>
                </div>
                <div>
                    <a href="{{ route('users.create') }}" 
                       class="inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600 px-4 py-2">
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

        <!-- Filtros -->
        <div class="p-6 border-b border-gray-200 dark:border-neutral-700">
            <form action="{{ route('users.index') }}" method="GET" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Búsqueda -->
                    <div class="relative">
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}"
                               class="py-2 px-3 ps-11 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-400 dark:focus:ring-neutral-600"
                               placeholder="Buscar usuarios...">
                        <div class="absolute inset-y-0 start-0 flex items-center pointer-events-none ps-4">
                            <svg class="size-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                            </svg>
                        </div>
                    </div>

                    <!-- Estado -->
                    <div>
                        <select name="status" 
                                class="py-2 px-3 pe-9 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-400 dark:focus:ring-neutral-600">
                            <option value="">Todos los estados</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Activos</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactivos</option>
                        </select>
                    </div>

                    <!-- Rol -->
                    <div>
                      <select name="role" class="py-2 px-3 pe-9 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-400 dark:focus:ring-neutral-600">
                          <option value="">Todos los roles</option>
                          @foreach($roles as $role)
                              <option value="{{ $role->name }}" {{ request('role') === $role->name ? 'selected' : '' }}>
                                  {{ $role->display_name }}
                              </option>
                          @endforeach
                      </select>
                  </div>

                    <!-- Unidad -->
                    <div>
                        <select name="unit" 
                                class="py-2 px-3 pe-9 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-400 dark:focus:ring-neutral-600">
                            <option value="">Todas las unidades</option>
                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}" {{ request('unit') == $unit->id ? 'selected' : '' }}>
                                    {{ $unit->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Botones de filtro -->
                <div class="flex justify-end gap-x-2">
                    <a href="{{ route('users.index') }}" 
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

        <!-- Mensajes de éxito/error -->
        @if(session('success'))
            <div class="m-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="m-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                {{ session('error') }}
            </div>
        @endif

        <!-- Tabla -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                <thead>
                    <tr>
                        <th scope="col" class="px-6 py-3 text-start">
                            <a href="{{ route('users.index', array_merge(request()->query(), [
                                'sort' => 'name',
                                'direction' => request('sort') === 'name' && request('direction') === 'asc' ? 'desc' : 'asc'
                            ])) }}" class="group inline-flex items-center gap-x-2 text-sm text-gray-500 dark:text-neutral-400">
                                Nombre
                                <svg class="flex-shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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
                          </th>
                          <th scope="col" class="px-6 py-3 text-start">
                              <a href="{{ route('users.index', array_merge(request()->query(), [
                                  'sort' => 'email',
                                  'direction' => request('sort') === 'email' && request('direction') === 'asc' ? 'desc' : 'asc'
                              ])) }}" class="group inline-flex items-center gap-x-2 text-sm text-gray-500 dark:text-neutral-400">
                                  Email
                                  <svg class="flex-shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                      @if(request('sort') === 'email')
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
                          <th scope="col" class="px-6 py-3 text-start">Unidad</th>
                          <th scope="col" class="px-6 py-3 text-start">Rol</th>
                          <th scope="col" class="px-6 py-3 text-start">Estado</th>
                          <th scope="col" class="px-6 py-3 text-end">Acciones</th>
                      </tr>
                  </thead>
                  <tbody class="divide-y divide-gray-200 dark:divide-neutral-700">
                      @forelse($users as $user)
                          <tr class="hover:bg-gray-50 dark:hover:bg-neutral-700">
                              <td class="px-6 py-4 whitespace-nowrap">
                                  <div class="flex items-center">
                                      <div class="flex-shrink-0 size-10">
                                          <img class="size-10 rounded-full" 
                                               src="{{ $user->profile_photo ? Storage::url($user->profile_photo) : asset('images/default-avatar.png') }}" 
                                               alt="{{ $user->name }}">
                                      </div>
                                      <div class="ms-4">
                                          <div class="text-sm font-medium text-gray-900 dark:text-neutral-200">
                                              {{ $user->name }}
                                          </div>
                                      </div>
                                  </div>
                              </td>
                              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-neutral-400">
                                  {{ $user->email }}
                              </td>
                              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-neutral-400">
                                  {{ $user->unit?->name ?? 'N/A' }}
                              </td>
                              <td class="p-3 whitespace-nowrap text-sm text-gray-500 dark:text-neutral-400">
                                @foreach($user->roles as $role)
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                        @switch($role->name)
                                            @case('admin')
                                                Líder de Calidad
                                                @break
                                            @case('agent')
                                                Profesional de Calidad
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
                              <td class="px-6 py-4 whitespace-nowrap">
                                  <span class="inline-flex items-center gap-1.5 py-0.5 px-2 rounded-full text-xs font-medium {{ $user->active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                      @if($user->active)
                                          <svg class="size-2.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                              <path d="M12 2c5.523 0 10 4.477 10 10s-4.477 10-10 10S2 17.523 2 12 6.477 2 12 2z"/>
                                              <path d="m9 12 2 2 4-4"/>
                                          </svg>
                                          Activo
                                      @else
                                          <svg class="size-2.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                              <circle cx="12" cy="12" r="10"/>
                                              <line x1="15" x2="9" y1="9" y2="15"/>
                                              <line x1="9" x2="15" y1="9" y2="15"/>
                                          </svg>
                                          Inactivo
                                      @endif
                                  </span>
                              </td>
                              <td class="px-6 py-4 whitespace-nowrap text-end text-sm font-medium">
                                  <div class="flex justify-end items-center gap-2">
                                      <!-- Ver detalles -->
                                      <button type="button" 
                                              data-hs-overlay="#user-modal-{{ $user->id }}"
                                              class="text-gray-500 hover:text-gray-700 dark:text-neutral-400 dark:hover:text-neutral-300">
                                          <svg class="size-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                              <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                                              <circle cx="12" cy="12" r="3"/>
                                          </svg>
                                      </button>
  
                                      <!-- Editar -->
                                      <a href="{{ route('users.edit', $user) }}" 
                                         class="text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                                          <svg class="size-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                              <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/>
                                              <path d="m15 5 4 4"/>
                                          </svg>
                                      </a>
  
                                      <!-- Toggle Estado -->
                                      <form action="{{ route('users.toggle-active', $user) }}" method="POST" class="inline">
                                          @csrf
                                          @method('PATCH')
                                          <button type="submit" 
                                                  class="{{ $user->active ? 'text-red-500 hover:text-red-700' : 'text-green-500 hover:text-green-700' }}"
                                                  onclick="return confirm('¿Estás seguro de que deseas {{ $user->active ? 'desactivar' : 'activar' }} este usuario?')">
                                              @if($user->active)
                                                  <svg class="size-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                      <rect width="18" height="18" x="3" y="3" rx="2"/>
                                                      <path d="M15 9h-6v6h6V9Z"/>
                                                  </svg>
                                              @else
                                                  <svg class="size-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                      <rect width="18" height="18" x="3" y="3" rx="2"/>
                                                      <path d="M9 12h6"/>
                                                  </svg>
                                              @endif
                                          </button>
                                      </form>
  
                                      <!-- Eliminar -->
                                      @if(auth()->id() !== $user->id)
                                          <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline">
                                              @csrf
                                              @method('DELETE')
                                              <button type="submit" 
                                                      class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300"
                                                      onclick="return confirm('¿Estás seguro de que deseas eliminar este usuario?')">
                                                  <svg class="size-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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
                              <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center dark:text-neutral-400">
                                  No se encontraron usuarios
                              </td>
                          </tr>
                      @endforelse
                  </tbody>
              </table>
          </div>
  
          <!-- Paginación -->
          <div class="px-6 py-4 border-t border-gray-200 dark:border-neutral-700">
              {{ $users->links() }}
          </div>
      </div>
  </div>
  
  <!-- Modales de detalles de usuario -->
@foreach($users as $user)
  <div id="user-modal-{{ $user->id }}" class="hs-overlay hidden w-full h-full fixed top-0 start-0 z-[60] overflow-x-hidden overflow-y-auto">
      <div class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all sm:max-w-lg sm:w-full m-3 sm:mx-auto">
          <div class="relative flex flex-col bg-white border shadow-sm rounded-xl dark:bg-neutral-800 dark:border-neutral-700">
              <div class="p-4 sm:p-10 overflow-y-auto">
                  <div class="mb-6 text-center">
                      <h3 class="mb-2 text-xl font-bold text-gray-800 dark:text-neutral-200">
                          Detalles del Usuario
                      </h3>
                  </div>

                  <div class="space-y-4">
                      <div class="text-center">
                          <img class="size-20 rounded-full mx-auto" 
                              src="{{ $user->profile_photo ? Storage::url($user->profile_photo) : asset('images/default-avatar.png') }}" 
                              alt="{{ $user->name }}">
                          <h4 class="mt-2 text-lg font-medium text-gray-800 dark:text-neutral-200">
                              {{ $user->name }}
                          </h4>
                          <p class="text-gray-500 dark:text-neutral-400">
                              {{ $user->email }}
                          </p>
                      </div>

                      <div class="grid grid-cols-2 gap-4">
                          <div>
                              <h5 class="text-sm font-medium text-gray-500 dark:text-neutral-400">Unidad</h5>
                              <p class="mt-1 text-sm text-gray-800 dark:text-neutral-200">
                                  {{ $user->unit?->name ?? 'No asignada' }}
                              </p>
                          </div>
                          <div>
                              <h5 class="text-sm font-medium text-gray-500 dark:text-neutral-400">Proceso</h5>
                              <p class="mt-1 text-sm text-gray-800 dark:text-neutral-200">
                                  {{ $user->process?->name ?? 'No asignado' }}
                              </p>
                          </div>
                          <div>
                              <h5 class="text-sm font-medium text-gray-500 dark:text-neutral-400">Cargo</h5>
                              <p class="mt-1 text-sm text-gray-800 dark:text-neutral-200">
                                  {{ $user->position?->name ?? 'No asignado' }}
                              </p>
                          </div>
                          <div>
                              <h5 class="text-sm font-medium text-gray-500 dark:text-neutral-400">Estado</h5>
                              <span class="inline-flex items-center gap-1.5 py-0.5 px-2 rounded-full text-xs font-medium {{ $user->active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                  @if($user->active)
                                      <svg class="size-2.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                          <path d="M12 2c5.523 0 10 4.477 10 10s-4.477 10-10 10S2 17.523 2 12 6.477 2 12 2z"/>
                                          <path d="m9 12 2 2 4-4"/>
                                      </svg>
                                      Activo
                                  @else
                                      <svg class="size-2.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                          <circle cx="12" cy="12" r="10"/>
                                          <line x1="15" x2="9" y1="9" y2="15"/>
                                          <line x1="9" x2="15" y1="9" y2="15"/>
                                      </svg>
                                      Inactivo
                                  @endif
                              </span>
                          </div>
                      </div>

                      <div>
                          <h5 class="text-sm font-medium text-gray-500 dark:text-neutral-400">Roles</h5>
                          <div class="mt-1 flex flex-wrap gap-2">
                              @foreach($user->roles as $role)
                                  <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                      @switch($role->name)
                                          @case('admin')
                                              Líder de Calidad
                                              @break
                                          @case('agent')
                                              Profesional de Calidad
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
                              <h5 class="text-sm font-medium text-gray-500 dark:text-neutral-400">Fecha de Creación</h5>
                              <p class="mt-1 text-sm text-gray-800 dark:text-neutral-200">
                                  {{ $user->created_at->format('d/m/Y H:i') }}
                              </p>
                          </div>
                          <div>
                              <h5 class="text-sm font-medium text-gray-500 dark:text-neutral-400">Última Actualización</h5>
                              <p class="mt-1 text-sm text-gray-800 dark:text-neutral-200">
                                  {{ $user->updated_at->format('d/m/Y H:i') }}
                              </p>
                          </div>
                      </div>
                  </div>
              </div>

              <div class="flex justify-end items-center gap-x-2 py-3 px-4 border-t dark:border-neutral-700">
                  <button type="button" 
                          class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:text-white dark:hover:bg-neutral-700 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600"
                          data-hs-overlay="#user-modal-{{ $user->id }}">
                      Cerrar
                  </button>
                  <a href="{{ route('users.edit', $user) }}" 
                    class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600">
                      Editar Usuario
                  </a>
              </div>
          </div>
      </div>
  </div>
@endforeach

@push('scripts')
<script>
    // Script para mantener los filtros en la URL durante la paginación
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

        // Limpiar filtros
        document.querySelector('[data-clear-filters]')?.addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = '{{ route('users.index') }}';
        });
    });
</script>
@endpush

@endsection