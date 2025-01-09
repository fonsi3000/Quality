<nav class="space-y-2">
    @can('admin.agent')
    <a href="{{ route('dashboard') }}" 
        class="flex items-center gap-x-3.5 py-2.5 px-3 text-sm font-medium {{ Request::routeIs('dashboard') ? 'text-gray-700 bg-gray-100 dark:bg-neutral-700 dark:text-white' : 'text-gray-600 hover:bg-gray-100 dark:hover:bg-neutral-700 dark:text-neutral-300' }} rounded-lg">
        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
        </svg>
        Tareas
    </a>
    @endcan

    @can('user')
    <!-- Menu simplificado para usuarios -->
    <div class="space-y-2">
        <a href="{{ route('documents.published') }}" 
            class="flex items-center gap-x-3.5 py-2.5 px-3 text-sm font-medium {{ Request::routeIs('documents.published') ? 'text-gray-700 bg-gray-100 dark:bg-neutral-700 dark:text-white' : 'text-gray-600 hover:bg-gray-100 dark:hover:bg-neutral-700 dark:text-neutral-300' }} rounded-lg">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
            </svg>
            Documentos Vigentes
        </a>

        <a href="{{ route('documents.requests.index') }}" 
            class="flex items-center gap-x-3.5 py-2.5 px-3 text-sm font-medium {{ Request::routeIs('documents.requests.*') ? 'text-gray-700 bg-gray-100 dark:bg-neutral-700 dark:text-white' : 'text-gray-600 hover:bg-gray-100 dark:hover:bg-neutral-700 dark:text-neutral-300' }} rounded-lg">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m6.75 12H5.625c-.621 0-1.125-.504-1.125-1.125v-17.25c0-.621.504-1.125 1.125-1.125h12.75c.621 0 1.125.504 1.125 1.125v17.25c0 .621-.504 1.125-1.125 1.125z" />
            </svg>
            Solicitud de Documentos
        </a>

        @if(Auth::user()->can('admin.agent') || (Auth::user()->process && Auth::user()->process->leader_id == Auth::id()))
        <a href="{{ route('documents.pending-leader') }}" 
            class="flex items-center gap-x-3.5 py-2.5 px-3 text-sm font-medium {{ Request::routeIs('documents.pending-leader') ? 'text-gray-700 bg-gray-100 dark:bg-neutral-700 dark:text-white' : 'text-gray-600 hover:bg-gray-100 dark:hover:bg-neutral-700 dark:text-neutral-300' }} rounded-lg">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z" />
            </svg>
            Documentos en Aprobación
        </a>
        @endif
    </div>
    @endcan

    <!-- Documentos Dropdown -->
@canany(['admin.only', 'admin.agent', 'view.agent'])
<div class="hs-accordion-group">
    <div class="hs-accordion" id="documents-accordion">
        <button type="button" 
                class="hs-accordion-toggle w-full flex items-center gap-x-3.5 py-2.5 px-3 text-sm font-medium text-gray-600 hover:bg-gray-100 dark:hover:bg-neutral-700 dark:text-neutral-300 rounded-lg">
            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Documentos
            <svg class="hs-accordion-active:rotate-180 ms-auto w-4 h-4 transition-transform duration-300" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="m6 9 6 6 6-6"/>
            </svg>
        </button>

        <div id="documents-submenu" class="hs-accordion-content hidden w-full overflow-hidden transition-[height] duration-300">
            <div class="ps-3">
                @canany(['admin.only', 'admin.agent'])
                <a href="{{ route('documents.published') }}" 
                    class="flex items-center gap-x-3.5 py-2.5 px-3 text-sm font-medium {{ Request::routeIs('documents.published') ? 'text-gray-700 bg-gray-100 dark:bg-neutral-700 dark:text-white' : 'text-gray-600 hover:bg-gray-100 dark:hover:bg-neutral-700 dark:text-neutral-300' }} rounded-lg">
                    Documentos Vigentes
                </a>

                <a href="{{ route('documents.masterdocument') }}" 
                    class="flex items-center gap-x-3.5 py-2.5 px-3 text-sm font-medium {{ Request::routeIs('documents.master') ? 'text-gray-700 bg-gray-100 dark:bg-neutral-700 dark:text-white' : 'text-gray-600 hover:bg-gray-100 dark:hover:bg-neutral-700 dark:text-neutral-300' }} rounded-lg">
                    Listado Maestro de Documentos
                </a>
                @endcanany

                <!-- Gestión de Documentos Submenu -->
                <div class="hs-accordion" id="doc-management-accordion">
                    <button type="button" 
                            class="hs-accordion-toggle w-full flex items-center gap-x-3.5 py-2.5 px-3 text-sm font-medium text-gray-600 hover:bg-gray-100 dark:hover:bg-neutral-700 dark:text-neutral-300 rounded-lg">
                        Gestión de Documentos
                        <svg class="hs-accordion-active:rotate-180 ms-auto w-4 h-4 transition-transform duration-300" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m6 9 6 6 6-6"/>
                        </svg>
                    </button>

                    <div id="doc-management-submenu" class="hs-accordion-content hidden w-full overflow-hidden transition-[height] duration-300">
                        <div class="ps-3">
                            @canany(['admin.only', 'admin.agent'])
                            <a href="{{ route('documents.requests.index') }}" 
                                class="flex items-center gap-x-3.5 py-2.5 px-3 text-sm font-medium {{ Request::routeIs('documents.requests.*') ? 'text-gray-700 bg-gray-100 dark:bg-neutral-700 dark:text-white' : 'text-gray-600 hover:bg-gray-100 dark:hover:bg-neutral-700 dark:text-neutral-300' }} rounded-lg">
                                Solicitud de Documentos
                            </a>

                            <a href="{{ route('documents.in-progress') }}" 
                                class="flex items-center gap-x-3.5 py-2.5 px-3 text-sm font-medium {{ Request::routeIs('documents.in-progress') ? 'text-gray-700 bg-gray-100 dark:bg-neutral-700 dark:text-white' : 'text-gray-600 hover:bg-gray-100 dark:hover:bg-neutral-700 dark:text-neutral-300' }} rounded-lg">
                                Documentos en Elaboración
                            </a>

                            <a href="{{ route('documents.in-review') }}" 
                                class="flex items-center gap-x-3.5 py-2.5 px-3 text-sm font-medium {{ Request::routeIs('documents.in-review') ? 'text-gray-700 bg-gray-100 dark:bg-neutral-700 dark:text-white' : 'text-gray-600 hover:bg-gray-100 dark:hover:bg-neutral-700 dark:text-neutral-300' }} rounded-lg">
                                Documentos en Revisión
                            </a>

                            <a href="{{ route('documents.pending-leader') }}" 
                                class="flex items-center gap-x-3.5 py-2.5 px-3 text-sm font-medium {{ Request::routeIs('documents.pending-leader') ? 'text-gray-700 bg-gray-100 dark:bg-neutral-700 dark:text-white' : 'text-gray-600 hover:bg-gray-100 dark:hover:bg-neutral-700 dark:text-neutral-300' }} rounded-lg">
                                Documentos en Aprobación
                            </a>
                            @endcanany
                        </div>
                    </div>
                </div>

                <!-- Ajustes de Documentos Submenu -->
                @canany(['admin.only', 'admin.agent'])
                <div class="hs-accordion" id="doc-settings-accordion">
                    <button type="button" 
                            class="hs-accordion-toggle w-full flex items-center gap-x-3.5 py-2.5 px-3 text-sm font-medium text-gray-600 hover:bg-gray-100 dark:hover:bg-neutral-700 dark:text-neutral-300 rounded-lg">
                        Ajustes de Documentos
                        <svg class="hs-accordion-active:rotate-180 ms-auto w-4 h-4 transition-transform duration-300" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m6 9 6 6 6-6"/>
                        </svg>
                    </button>

                    <div id="doc-settings-submenu" class="hs-accordion-content hidden w-full overflow-hidden transition-[height] duration-300">
                        <div class="ps-3">
                            <a href="{{ route('document-types.index') }}" 
                                class="flex items-center gap-x-3.5 py-2.5 px-3 text-sm font-medium {{ Request::routeIs('document-types.*') ? 'text-gray-700 bg-gray-100 dark:bg-neutral-700 dark:text-white' : 'text-gray-600 hover:bg-gray-100 dark:hover:bg-neutral-700 dark:text-neutral-300' }} rounded-lg">
                                Tipos de Documentos
                            </a>

                            <a href="{{ route('document-templates.index') }}" 
                                class="flex items-center gap-x-3.5 py-2.5 px-3 text-sm font-medium {{ Request::routeIs('document-templates.*') ? 'text-gray-700 bg-gray-100 dark:bg-neutral-700 dark:text-white' : 'text-gray-600 hover:bg-gray-100 dark:hover:bg-neutral-700 dark:text-neutral-300' }} rounded-lg">
                                Plantillas de Documentos
                            </a>
                        </div>
                    </div>
                </div>
                @endcanany
            </div>
        </div>
    </div>
</div>
@endcanany

<!-- Organización Dropdown -->
@can('admin.agent')
<div class="hs-accordion" id="organization-accordion">
    <button type="button" 
            class="hs-accordion-toggle w-full flex items-center gap-x-3.5 py-2.5 px-3 text-sm font-medium text-gray-600 hover:bg-gray-100 dark:hover:bg-neutral-700 dark:text-neutral-300 rounded-lg">
        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
        </svg>
        Organización
        <svg class="hs-accordion-active:rotate-180 ms-auto w-4 h-4 transition-transform duration-300" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="m6 9 6 6 6-6"/>
        </svg>
    </button>

    <div id="organization-submenu" class="hs-accordion-content hidden w-full overflow-hidden transition-[height] duration-300 ps-3">
        <a href="{{ route('units.index') }}" 
            class="flex items-center gap-x-3.5 py-2.5 px-3 text-sm font-medium {{ Request::routeIs('units.*') ? 'text-gray-700 bg-gray-100 dark:bg-neutral-700 dark:text-white' : 'text-gray-600 hover:bg-gray-100 dark:hover:bg-neutral-700 dark:text-neutral-300' }} rounded-lg">
            Empresas
        </a>
        
        <a href="{{ route('positions.index') }}" 
            class="flex items-center gap-x-3.5 py-2.5 px-3 text-sm font-medium {{ Request::routeIs('positions.*') ? 'text-gray-700 bg-gray-100 dark:bg-neutral-700 dark:text-white' : 'text-gray-600 hover:bg-gray-100 dark:hover:bg-neutral-700 dark:text-neutral-300' }} rounded-lg">
            Cargos
        </a>
        
        <a href="{{ route('processes.index') }}" 
            class="flex items-center gap-x-3.5 py-2.5 px-3 text-sm font-medium {{ Request::routeIs('processes.*') ? 'text-gray-700 bg-gray-100 dark:bg-neutral-700 dark:text-white' : 'text-gray-600 hover:bg-gray-100 dark:hover:bg-neutral-700 dark:text-neutral-300' }} rounded-lg">
            Lista de procesos
        </a>
        
        <a href="{{ route('users.index') }}" 
            class="flex items-center gap-x-3.5 py-2.5 px-3 text-sm font-medium {{ Request::routeIs('users.*') ? 'text-gray-700 bg-gray-100 dark:bg-neutral-700 dark:text-white' : 'text-gray-600 hover:bg-gray-100 dark:hover:bg-neutral-700 dark:text-neutral-300' }} rounded-lg">
            Usuarios
        </a>
    </div>
</div>
@endcan
</nav>