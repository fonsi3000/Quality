<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Qualy') }} - @yield('title', 'Dashboard')</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            .dt-layout-row:has(.dt-search),
            .dt-layout-row:has(.dt-length),
            .dt-layout-row:has(.dt-paging) {
              display: none !important;
            }
          </style>

        <!-- Additional Styles -->
        @stack('styles')
    </head>
    <body class="bg-gray-50 dark:bg-neutral-900">
        <div class="flex h-full">
            <!-- Sidebar -->
            <aside class="fixed top-0 left-0 z-40 w-64 h-screen transition-transform -translate-x-full lg:translate-x-0">
                <div class="h-full px-4 py-6 overflow-y-auto bg-white dark:bg-neutral-800 border-r border-gray-200 dark:border-neutral-700">
                    <!-- Logo -->
                    <div class="flex items-center justify-center mb-4">
                        <a href="#" class="flex items-center">
                            <img src="{{ asset('images/logo.png') }}" class="h-16 w-auto" alt="Logo">
                        </a>
                    </div>

                    <!-- Navigation -->
                    <nav class="space-y-2">
                        <!-- Tareas -->
                        <a href="{{ route('dashboard') }}" 
                        class="flex items-center gap-x-3.5 py-2.5 px-3 text-sm font-medium {{ Request::routeIs('dashboard') ? 'text-gray-700 bg-gray-100 dark:bg-neutral-700 dark:text-white' : 'text-gray-600 hover:bg-gray-100 dark:hover:bg-neutral-700 dark:text-neutral-300' }} rounded-lg">
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            Tareas
                        </a>

                        <!-- Organización Dropdown -->
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
                                <!-- Unidades Submenu -->
                                <a href="{{ route('units.index') }}" 
                                   class="flex items-center gap-x-3.5 py-2.5 px-3 text-sm font-medium {{ Request::routeIs('units.*') ? 'text-gray-700 bg-gray-100 dark:bg-neutral-700 dark:text-white' : 'text-gray-600 hover:bg-gray-100 dark:hover:bg-neutral-700 dark:text-neutral-300' }} rounded-lg">
                                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                    Unidades
                                </a>
                            
                                <!-- Cargos Submenu -->
                                <a href="{{ route('positions.index') }}" 
                                   class="flex items-center gap-x-3.5 py-2.5 px-3 text-sm font-medium {{ Request::routeIs('positions.*') ? 'text-gray-700 bg-gray-100 dark:bg-neutral-700 dark:text-white' : 'text-gray-600 hover:bg-gray-100 dark:hover:bg-neutral-700 dark:text-neutral-300' }} rounded-lg">
                                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    Cargos
                                </a>
                            
                                <!-- Lista de procesos Submenu -->
                                <a href="{{ route('processes.index') }}" 
                                   class="flex items-center gap-x-3.5 py-2.5 px-3 text-sm font-medium {{ Request::routeIs('processes.*') ? 'text-gray-700 bg-gray-100 dark:bg-neutral-700 dark:text-white' : 'text-gray-600 hover:bg-gray-100 dark:hover:bg-neutral-700 dark:text-neutral-300' }} rounded-lg">
                                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                    </svg>
                                    Lista de procesos
                                </a>
                            
                                <!-- Usuarios Submenu -->
                                <a href="{{ route('users.index') }}" 
                                   class="flex items-center gap-x-3.5 py-2.5 px-3 text-sm font-medium {{ Request::routeIs('users.*') ? 'text-gray-700 bg-gray-100 dark:bg-neutral-700 dark:text-white' : 'text-gray-600 hover:bg-gray-100 dark:hover:bg-neutral-700 dark:text-neutral-300' }} rounded-lg">
                                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                    </svg>
                                    Usuarios
                                </a>
                            </div>
                        </div>
                    </nav>

                    <!-- Agregar el script para el acordeón -->
                    @push('scripts')
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            // Verificar si estamos en alguna ruta de organización
                            const isOrganizationRoute = {{ Request::routeIs('users.*') || Request::routeIs('units.*') || Request::routeIs('processes.*') || Request::routeIs('positions.*') ? 'true' : 'false' }};
                            
                            if (isOrganizationRoute) {
                                // Obtener el acordeón y expandirlo
                                const accordion = document.getElementById('organization-accordion');
                                const content = document.getElementById('organization-submenu');
                                
                                if (accordion && content) {
                                    accordion.classList.add('hs-accordion-active');
                                    content.classList.remove('hidden');
                                    // Ajustar la altura del contenido
                                    content.style.height = content.scrollHeight + 'px';
                                }
                            }
                        });
                    </script>
                    @endpush
                </div>
            </aside>

            <!-- Content Area -->
            <div class="lg:ml-64 flex flex-col flex-1">
                <!-- Top Navbar -->
                <nav class="bg-white dark:bg-neutral-800 border-b border-gray-200 dark:border-neutral-700">
                    <div class="px-4 py-3 lg:px-6">
                        <div class="flex items-center justify-between">
                            <!-- Mobile Menu Button -->
                            <button type="button" class="lg:hidden text-gray-600 hover:text-gray-900 focus:outline-none" data-hs-overlay="#mobile-menu">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                            </button>

                            <!-- Search Bar -->
                            <div class="hidden md:block flex-1 max-w-md ml-4">
                                {{-- <div class="relative">
                                    <input type="search" class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-200 dark:border-neutral-700 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-neutral-800 dark:text-neutral-300" placeholder="Search...">
                                    <div class="absolute left-3 top-2.5">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </div>
                                </div> --}}
                            </div>

                            <!-- Right Navigation Items -->
                            <div class="flex items-center gap-3">
                                <!-- Notifications -->
                                <button type="button" class="p-2 text-gray-600 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-neutral-300 dark:hover:bg-neutral-700">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                    </svg>
                                </button>

                                <!-- Profile Dropdown -->
                                <div class="hs-dropdown relative inline-flex">
                                    <button type="button" class="inline-flex items-center gap-2" data-hs-dropdown-toggle>
                                        <img class="w-10 h-10 rounded-full" src="https://images.unsplash.com/photo-1568602471122-7832951cc4c5?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=facearea&facepad=2&w=300&h=300&q=80" alt="User">
                                        <span class="hidden md:block text-sm font-medium text-gray-600 dark:text-neutral-300">
                                            {{ Auth::user()->name }}
                                        </span>
                                    </button>

                                    <div class="hs-dropdown-menu hidden transition-[opacity,margin] duration hs-dropdown-open:opacity-100 opacity-0 min-w-48 bg-white shadow-md rounded-lg p-2 mt-2 dark:bg-neutral-800 dark:border dark:border-neutral-700" aria-labelledby="hs-dropdown-with-header">
                                        <div class="py-3 px-5 -m-2 bg-gray-100 rounded-t-lg dark:bg-neutral-700">
                                            <p class="text-sm text-gray-500 dark:text-neutral-400">Conectado como</p>
                                            <p class="text-sm font-medium text-gray-800 dark:text-neutral-300">
                                                {{ Auth::user()->email }}
                                            </p>
                                        </div>
                                        <div class="mt-2 py-2 first:pt-0 last:pb-0">
                                            <a class="flex items-center gap-x-3.5 py-2 px-3 text-sm text-gray-800 rounded-lg hover:bg-gray-100 dark:text-neutral-300 dark:hover:bg-neutral-700" href="#">
                                                Perfil
                                            </a>
                                            <!-- Formulario de Logout -->
                                            <form method="POST" action="{{ route('logout') }}" class="w-full">
                                                @csrf
                                                <button type="submit" 
                                                        class="w-full text-left flex items-center gap-x-3.5 py-2 px-3 text-sm text-red-600 rounded-lg hover:bg-gray-100 dark:text-red-500 dark:hover:bg-neutral-700">
                                                    Cerrar sesión
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </nav>

                <!-- Main Content -->
                <main class="flex-1 p-4 lg:p-6 bg-gray-50 dark:bg-neutral-900">
                    @yield('content')
                </main>
            </div>
        </div>

        <!-- Mobile Menu (Off-canvas) -->
        <div id="mobile-menu" class="hs-overlay hidden fixed top-0 left-0 z-[60] w-64 h-full transform -translate-x-full transition-all duration-300">
            <!-- Mobile menu content -->
            <div class="h-full px-4 py-6 overflow-y-auto bg-white dark:bg-neutral-800 border-r border-gray-200 dark:border-neutral-700">
                <nav class="space-y-2">
                    <!-- Mobile Navigation Items (Same as sidebar) -->
                    <a href="#" class="flex items-center gap-x-3.5 py-2.5 px-3 text-sm font-medium text-gray-700 rounded-lg bg-gray-100 dark:bg-neutral-700 dark:text-white dark:hover:bg-neutral-700">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Dashboard
                    </a>
                </nav>
            </div>
        </div>

        <!-- Scripts -->
        @stack('scripts')
    </body>
</html>