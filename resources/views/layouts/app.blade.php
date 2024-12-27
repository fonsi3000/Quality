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
            <aside class="fixed top-0 left-0 z-40 w-80 h-screen transition-transform -translate-x-full lg:translate-x-0">
                <div class="h-full px-4 py-6 overflow-y-auto bg-white dark:bg-neutral-800 border-r border-gray-200 dark:border-neutral-700">
                    <!-- Logo -->
                    <div class="flex items-center justify-center mb-4">
                        <a href="#" class="flex items-center">
                            <img src="{{ asset('images/logo.png') }}" class="h-16 w-auto" alt="Logo">
                        </a>
                    </div>

                    <!-- Navigation -->
                    @include('layouts.menu')
                </div>
            </aside>

            <!-- Content Area -->
            <div class="lg:ml-80 flex flex-col flex-1">
                <!-- Top Navbar -->
                <nav class="bg-white dark:bg-neutral-800 border-b border-gray-200 dark:border-neutral-700">
                    <div class="px-4 py-3 lg:px-6">
                        <div class="flex items-center justify-between">
                            <!-- Mobile Menu Button -->
                            <button type="button" 
                                    class="lg:hidden inline-flex items-center justify-center p-2 rounded-lg text-gray-600 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-neutral-300 dark:hover:bg-neutral-700" 
                                    data-hs-overlay="#mobile-menu"
                                    aria-controls="mobile-menu"
                                    aria-label="Toggle navigation">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                            </button>

                            <!-- Search Bar -->
                            <div class="hidden md:block flex-1 max-w-md ml-4">
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
                                <div class="hs-dropdown hs-dropdown-up relative inline-flex">
                                    <button type="button" class="inline-flex items-center gap-2" data-hs-dropdown-toggle>
                                        @if(Auth::user()->profile_photo)
                                            <img class="w-10 h-10 rounded-full object-cover" src="{{ asset('storage/' . Auth::user()->profile_photo) }}" alt="{{ Auth::user()->name }}">
                                        @else
                                            <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center">
                                                <span class="text-gray-600 text-lg font-medium">{{ substr(Auth::user()->name, 0, 1) }}</span>
                                            </div>
                                        @endif
                                        <span class="hidden md:block text-sm font-medium text-gray-600 dark:text-neutral-300">
                                            {{ Auth::user()->name }}
                                        </span>
                                    </button>

                                    <div class="hs-dropdown-menu hidden transition-[opacity,margin] duration hs-dropdown-open:opacity-100 opacity-0 min-w-48 bg-white shadow-md rounded-lg p-2 mb-2 bottom-full dark:bg-neutral-800 dark:border dark:border-neutral-700 z-[9999] absolute left-0" aria-labelledby="hs-dropdown-with-header">
                                        <div class="py-3 px-5 -m-2 bg-gray-100 rounded-t-lg dark:bg-neutral-700">
                                            <p class="text-sm text-gray-500 dark:text-neutral-400">Conectado como</p>
                                            <p class="text-sm font-medium text-gray-800 dark:text-neutral-300">
                                                {{ Auth::user()->email }}
                                            </p>
                                        </div>
                                        <div class="mt-2 py-2 first:pt-0 last:pb-0">
                                            <form method="POST" action="{{ route('logout') }}" class="w-full">
                                                @csrf
                                                <button type="submit" class="w-full text-left flex items-center gap-x-3.5 py-2 px-3 text-sm text-red-600 rounded-lg hover:bg-gray-100 dark:text-red-500 dark:hover:bg-neutral-700">
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

        <!-- Mobile Menu (Off-canvas) --><!-- Mobile Menu (Off-canvas) -->
        <div id="mobile-menu" 
        class="hs-overlay hs-overlay-open:translate-x-0 -translate-x-full fixed top-0 start-0 transition-all duration-300 transform h-full max-w-xs w-full z-[60] bg-white border-e dark:bg-neutral-800 dark:border-neutral-700 hidden" 
        tabindex="-1">
       <div class="flex justify-between items-center py-3 px-4 border-b dark:border-neutral-700">
           <!-- Logo -->
           <div class="flex items-center justify-center mb-4">
                <a href="#" class="flex items-center">
                    <img src="{{ asset('images/logo.png') }}" class="h-16 w-auto" alt="Logo">
                </a>
            </div>
           <button type="button" 
                   class="hs-overlay-close flex justify-center items-center w-8 h-8 text-sm font-semibold rounded-full border border-transparent text-gray-800 hover:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:text-white dark:hover:bg-neutral-700" 
                   data-hs-overlay="#mobile-menu">
               <span class="sr-only">Cerrar</span>
               <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                   <path d="M18 6L6 18M6 6l12 12"/>
               </svg>
           </button>
       </div>
       <div class="p-4">
           <!-- Mobile Navigation Items -->
           @include('layouts.menu')
       </div>
   </div>

   <!-- Scripts -->
   @stack('scripts')
   <script>
       // Inicialización de componentes HS
       document.addEventListener('DOMContentLoaded', function () {
           // Inicializar HSOverlay
           if (typeof HSOverlay !== 'undefined') {
               HSOverlay.init();
           }

           // Inicializar HSDropdown
           if (typeof HSDropdown !== 'undefined') {
               HSDropdown.init();
           }

           // Inicializar HSAccordion
           if (typeof HSAccordion !== 'undefined') {
               const accordions = document.querySelectorAll('.hs-accordion');
               accordions.forEach(accordion => {
                   new HSAccordion(accordion);
               });
           }

           // Manejar el cierre del menú móvil al hacer clic en un enlace
           const mobileMenu = document.getElementById('mobile-menu');
           if (mobileMenu) {
               const links = mobileMenu.getElementsByTagName('a');
               Array.from(links).forEach(link => {
                   link.addEventListener('click', () => {
                       HSOverlay.close(mobileMenu);
                   });
               });
           }
       });

       // Script para los acordeones (lo movemos aquí para asegurar que se ejecute después de la carga del DOM)
       document.addEventListener('DOMContentLoaded', function() {
           // Verificar si estamos en alguna ruta de organización
           const isOrganizationRoute = {{ Request::routeIs('users.*') || Request::routeIs('units.*') || Request::routeIs('processes.*') || Request::routeIs('positions.*') ? 'true' : 'false' }};
           
           // Verificar si estamos en alguna ruta de gestión de documentos
           const isDocumentManagementRoute = {{ Request::routeIs('documents.requests.*') || Request::routeIs('documents.in-progress') || Request::routeIs('documents.in-review') ? 'true' : 'false' }};
           
           // Verificar si estamos en alguna ruta de configuración de documentos
           const isDocumentConfigRoute = {{ Request::routeIs('document-types.*') || Request::routeIs('document-templates.*') ? 'true' : 'false' }};
           
           // Función para expandir acordeón
           const expandAccordion = (accordionId, submenuId) => {
               const accordion = document.getElementById(accordionId);
               const content = document.getElementById(submenuId);
               
               if (accordion && content) {
                   accordion.classList.add('hs-accordion-active');
                   content.classList.remove('hidden');
                   content.style.height = content.scrollHeight + 'px';
               }
           };
           
           // Expandir acordeones según la ruta actual
           if (isOrganizationRoute) {
               expandAccordion('organization-accordion', 'organization-submenu');
           }
           
           if (isDocumentManagementRoute) {
               expandAccordion('document-management-accordion', 'document-management-submenu');
           }
           
           if (isDocumentConfigRoute) {
               expandAccordion('document-config-accordion', 'document-config-submenu');
           }
       });
   </script>
</body>
</html>