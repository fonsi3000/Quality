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
                @can('admin.only')
                <aside class="fixed top-0 left-0 z-40 w-80 h-screen transition-transform -translate-x-full lg:translate-x-0">
                    <div class="h-full px-4 py-6 overflow-y-auto bg-white dark:bg-neutral-800 border-r border-gray-200 dark:border-neutral-700">
                        <!-- Logo -->
                        <div class="flex items-center justify-center mb-4">
                            <a href="#" class="flex items-center">
                                <img src="{{ asset('images/logos.png') }}" class="h-16 w-auto" alt="Logo">
                            </a>
                        </div>

                        <!-- Navigation -->
                        @include('layouts.menu')
                    </div>
                </aside>
                @endcan

            <!-- Content Area -->
            <div class="{{ Auth::user()->can('admin.only') ? 'lg:ml-80' : '' }} flex flex-col flex-1">
                <!-- Top Navbar -->
                <nav class="bg-white dark:bg-neutral-800 border-b border-gray-200 dark:border-neutral-700">
                    <div class="px-4 py-3 lg:px-6">
                        <div class="flex items-center justify-between">
                            <!-- Mobile Menu Button - solo para admins -->
                            @can('admin.only')
                            <button type="button" 
                                    class="lg:hidden inline-flex items-center justify-center p-2 rounded-lg text-gray-600 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-neutral-300 dark:hover:bg-neutral-700" 
                                    data-hs-overlay="#mobile-menu"
                                    aria-controls="mobile-menu"
                                    aria-label="Toggle navigation">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                            </button>
                            @endcan

                            <!-- Search Bar -->
                            <div class="hidden md:block flex-1 max-w-md {{ Auth::user()->can('admin.only') ? 'ml-4' : '' }}">
                            </div>

                            <!-- Right Navigation Items -->
                            <div class="flex items-center gap-3 {{ Auth::user()->can('admin.only') ? '' : 'ml-auto' }}">
                                <!-- Notifications -->
                                <button type="button" class="p-2 text-gray-600 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-neutral-300 dark:hover:bg-neutral-700">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                    </svg>
                                </button>

                                <!-- Botón "Volver al inicio" que cierra sesión y redirige -->
                                <button
                                    type="button"
                                    onclick="logoutAndGoHome()"
                                    class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    aria-label="Volver al inicio"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l7-7m0 0l7 7m-7-7v18" />
                                    </svg>
                                    <span class="text-sm font-medium">Volver al inicio</span>
                                </button>
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
        <div id="mobile-menu" 
            class="hs-overlay hs-overlay-open:translate-x-0 -translate-x-full fixed top-0 start-0 transition-all duration-300 transform h-full max-w-xs w-full z-[60] bg-white border-e dark:bg-neutral-800 dark:border-neutral-700 hidden" 
            tabindex="-1">
            <div class="flex justify-between items-center py-3 px-4 border-b dark:border-neutral-700">
                <!-- Logo -->
                <div class="flex items-center justify-center mb-4">
                    <a href="#" class="flex items-center">
                        <img src="{{ asset('images/logos.png') }}" class="h-16 w-auto" alt="Logo">
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
            // URL fija del dashboard Angular
            const DASHBOARD_URL = 'https://app.espumasmedellin-litoral.com/dashboard';

            // Cierra la sesión en Laravel (si es posible) y redirige al dashboard del frontend
            async function logoutAndGoHome() {
                const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                try {
                    // Intento principal: POST /logout (CSRF)
                    let res = await fetch('/logout', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrf,
                            'Accept': 'application/json'
                        },
                        credentials: 'same-origin'
                    });

                    // Fallback si solo aceptas GET
                    if (!res.ok && res.status === 405) {
                        await fetch('/logout', { method: 'GET', credentials: 'same-origin' });
                    }
                } catch (e) {
                    // Ignorar errores de red/timeouts: igual redirigimos
                } finally {
                    window.location.href = DASHBOARD_URL;
                }
            }

            document.addEventListener('DOMContentLoaded', function () {
                // Inicializar HSOverlay
                if (typeof HSOverlay !== 'undefined') {
                    HSOverlay.init();
                }

                // Inicializar HSDropdown
                if (typeof HSDropdown !== 'undefined') {
                    HSDropdown.init();
                }

                // Inicializar HSAccordion para el grupo principal
                if (typeof HSAccordion !== 'undefined') {
                    // Inicializar grupos de acordeones
                    const accordionGroups = document.querySelectorAll('.hs-accordion-group');
                    accordionGroups.forEach(group => {
                        HSAccordion.init(group);
                    });

                    // Inicializar acordeones individuales que no estén dentro de grupos
                    const standaloneAccordions = document.querySelectorAll('.hs-accordion:not(.hs-accordion-group .hs-accordion)');
                    standaloneAccordions.forEach(accordion => {
                        HSAccordion.init(accordion);
                    });
                }

                // Manejar el cierre del menú móvil al hacer clic en un enlace
                const mobileMenu = document.getElementById('mobile-menu');
                if (mobileMenu) {
                    const links = mobileMenu.getElementsByTagName('a');
                    Array.from(links).forEach(link => {
                        link.addEventListener('click', () => {
                            if (typeof HSOverlay !== 'undefined') {
                                HSOverlay.close(mobileMenu);
                            }
                        });
                    });
                }
            });

            // Script para los acordeones (expansión según la ruta actual)
            document.addEventListener('DOMContentLoaded', function() {
                // Verificar rutas
                const isOrganizationRoute = {{ Request::routeIs('users.*') || Request::routeIs('units.*') || Request::routeIs('processes.*') || Request::routeIs('positions.*') ? 'true' : 'false' }};
                
                const isDocumentsRoute = {{ Request::routeIs('documents.*') ? 'true' : 'false' }};
                
                const isDocumentManagementRoute = {{ 
                    Request::routeIs('documents.requests.*') || 
                    Request::routeIs('documents.in-progress') || 
                    Request::routeIs('documents.in-review') || 
                    Request::routeIs('documents.pending-leader') ? 'true' : 'false' 
                }};
                
                const isDocumentSettingsRoute = {{ 
                    Request::routeIs('document-types.*') || 
                    Request::routeIs('document-templates.*') ? 'true' : 'false' 
                }};

                // Función mejorada para expandir acordeón
                const expandAccordion = (accordionId) => {
                    const accordion = document.querySelector(`#${accordionId}`);
                    if (accordion) {
                        const toggle = accordion.querySelector('.hs-accordion-toggle');
                        const content = accordion.querySelector('.hs-accordion-content');
                        
                        if (toggle && content) {
                            // Expandir el acordeón
                            toggle.setAttribute('aria-expanded', 'true');
                            content.classList.remove('hidden');
                            content.style.height = 'auto';
                            accordion.classList.add('hs-accordion-active');

                            // Si es un subacordeón, expandir también el padre
                            const parentAccordion = accordion.closest('.hs-accordion-group > .hs-accordion');
                            if (parentAccordion && parentAccordion.id !== accordionId) {
                                expandAccordion(parentAccordion.id);
                            }
                        }
                    }
                };

                // Función para verificar si un acordeón está activo
                const isAccordionActive = (accordionId) => {
                    const accordion = document.querySelector(`#${accordionId}`);
                    return accordion && accordion.classList.contains('hs-accordion-active');
                };

                // Expandir acordeones basado en la ruta actual
                if (isOrganizationRoute) {
                    expandAccordion('organization-accordion');
                }

                if (isDocumentsRoute || isDocumentManagementRoute || isDocumentSettingsRoute) {
                    // Siempre expandir el acordeón principal de documentos primero
                    expandAccordion('documents-accordion');
                    
                    // Luego expandir los subacordeones según la ruta
                    if (isDocumentManagementRoute) {
                        expandAccordion('doc-management-accordion');
                    }
                    
                    if (isDocumentSettingsRoute) {
                        expandAccordion('doc-settings-accordion');
                    }
                }

                // Manejar la expansión manual de acordeones
                document.querySelectorAll('.hs-accordion-toggle').forEach(toggle => {
                    toggle.addEventListener('click', () => {
                        const accordion = toggle.closest('.hs-accordion');
                        if (accordion) {
                            const isExpanding = !isAccordionActive(accordion.id);
                            if (isExpanding) {
                                // Si estamos expandiendo, asegurarnos de que el padre también esté expandido
                                const parentAccordion = accordion.closest('.hs-accordion-group > .hs-accordion');
                                if (parentAccordion && parentAccordion.id !== accordion.id) {
                                    expandAccordion(parentAccordion.id);
                                }
                            }
                        }
                    });
                });
            });
        </script>
    </body>
</html>
