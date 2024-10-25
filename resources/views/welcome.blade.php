<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Qualy') }} - @yield('title', 'Welcome')</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet">

        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <style>
                /* ! tailwindcss v3.4.1 | MIT License | https://tailwindcss.com */
                *,::after,::before{box-sizing:border-box;border-width:0;border-style:solid;border-color:#e5e7eb}::after,::before{--tw-content:''}:host,html{line-height:1.5;-webkit-text-size-adjust:100%;-moz-tab-size:4;tab-size:4;font-family:Figtree, ui-sans-serif, system-ui, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji;font-feature-settings:normal;font-variation-settings:normal;-webkit-tap-highlight-color:transparent}body{margin:0;line-height:inherit}hr{height:0;color:inherit;border-top-width:1px}abbr:where([title]){-webkit-text-decoration:underline dotted;text-decoration:underline dotted}h1,h2,h3,h4,h5,h6{font-size:inherit;font-weight:inherit}a{color:inherit;text-decoration:inherit}b,strong{font-weight:bolder}code,kbd,pre,samp{font-family:ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;font-feature-settings:normal;font-variation-settings:normal;font-size:1em}small{font-size:80%}sub,sup{font-size:75%;line-height:0;position:relative;vertical-align:baseline}sub{bottom:-.25em}sup{top:-.5em}table{text-indent:0;border-color:inherit;border-collapse:collapse}button,input,optgroup,select,textarea{font-family:inherit;font-feature-settings:inherit;font-variation-settings:inherit;font-size:100%;font-weight:inherit;line-height:inherit;color:inherit;margin:0;padding:0}button,select{text-transform:none}[type=button],[type=reset],[type=submit],button{-webkit-appearance:button;background-color:transparent;background-image:none}:-moz-focusring{outline:auto}:-moz-ui-invalid{box-shadow:none}progress{vertical-align:baseline}::-webkit-inner-spin-button,::-webkit-outer-spin-button{height:auto}[type=search]{-webkit-appearance:textfield;outline-offset:-2px}::-webkit-search-decoration{-webkit-appearance:none}::-webkit-file-upload-button{-webkit-appearance:button;font:inherit}summary{display:list-item}blockquote,dd,dl,figure,h1,h2,h3,h4,h5,h6,hr,p,pre{margin:0}fieldset{margin:0;padding:0}legend{padding:0}menu,ol,ul{list-style:none;margin:0;padding:0}textarea{resize:vertical}input::placeholder,textarea::placeholder{opacity:1;color:#9ca3af}[role=button],button{cursor:pointer}:disabled{cursor:default}audio,canvas,embed,iframe,img,object,svg,video{display:block;vertical-align:middle}img,video{max-width:100%;height:auto}[hidden]{display:none}
            </style>
        @endif

        <!-- Additional Styles -->
        @stack('styles')
    </head>

    <body class="font-sans antialiased dark:bg-black dark:text-white/50">
        <div class="bg-gray-50 text-black/50 dark:bg-black dark:text-white/50">            
                <!-- Navigation Header -->
                <header class="flex flex-wrap md:justify-start md:flex-nowrap z-50 w-full py-2">
                    <nav class="relative max-w-7xl w-full flex flex-wrap md:grid md:grid-cols-12 basis-full items-center px-4 md:px-6 md:px-8 mx-auto">
                        <!-- Logo Section -->
                        <div class="md:col-span-3">
                            <a class="flex-none rounded-xl text-xl inline-block font-semibold focus:outline-none focus:opacity-80"
                                href="#"
                                aria-label="Qualy">
                                <img src="{{ asset('images/logo.png') }}"
                                    alt="Qualy Logo"
                                    class="w-40 md:w-48 lg:w-56 h-auto object-contain">
                            </a>
                        </div>
                        
                        <!-- Navigation Buttons -->
                        <div class="flex items-center gap-x-1 md:gap-x-2 ms-auto py-1 md:ps-6 md:order-3 md:col-span-3">
                            <!-- Sign In Button -->
                            <button type="button" 
                                    class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-xl border border-transparent bg-lime-400 text-black hover:bg-lime-500 focus:outline-none focus:bg-lime-500 transition disabled:opacity-50 disabled:pointer-events-none" 
                                    data-hs-overlay="#hs-modal-signin">
                                Sign in
                            </button>
                            
                            <!-- Mobile Menu Toggle -->
                            <div class="md:hidden">
                                <button type="button" 
                                        class="hs-collapse-toggle size-[38px] flex justify-center items-center text-sm font-semibold rounded-xl border border-gray-200 text-black hover:bg-gray-100 focus:outline-none focus:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:text-white dark:border-neutral-700 dark:hover:bg-neutral-700 dark:focus:bg-neutral-700" 
                                        id="hs-navbar-collapse" 
                                        data-hs-collapse="#navbar-collapse"
                                        aria-controls="navbar-collapse" 
                                        aria-label="Toggle navigation">
                                    <!-- Hamburger Icon -->
                                    <svg class="hs-collapse-open:hidden shrink-0 size-4" 
                                         xmlns="http://www.w3.org/2000/svg" 
                                         width="24" height="24" 
                                         viewBox="0 0 24 24" 
                                         fill="none" 
                                         stroke="currentColor" 
                                         stroke-width="2" 
                                         stroke-linecap="round" 
                                         stroke-linejoin="round">
                                        <line x1="3" x2="21" y1="6" y2="6"/>
                                        <line x1="3" x2="21" y1="12" y2="12"/>
                                        <line x1="3" x2="21" y1="18" y2="18"/>
                                    </svg>
                                    <!-- Close Icon -->
                                    <svg class="hs-collapse-open:block hidden shrink-0 size-4" 
                                         xmlns="http://www.w3.org/2000/svg" 
                                         width="24" height="24" 
                                         viewBox="0 0 24 24" 
                                         fill="none" 
                                         stroke="currentColor" 
                                         stroke-width="2" 
                                         stroke-linecap="round" 
                                         stroke-linejoin="round">
                                        <path d="M18 6 6 18"/>
                                        <path d="m6 6 12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Navigation Menu -->
                        <div id="navbar-collapse" 
                             class="hs-collapse hidden overflow-hidden transition-all duration-300 basis-full grow md:block md:w-auto md:basis-auto md:order-2 md:col-span-6">
                            <div class="flex flex-col gap-y-4 gap-x-0 mt-5 md:flex-row md:justify-center md:items-center md:gap-y-0 md:gap-x-7 md:mt-0">
                                <!-- Navigation Links -->
                                <div>
                                    <a class="relative inline-block text-black focus:outline-none before:absolute before:bottom-0.5 before:start-0 before:-z-[1] before:w-full before:h-1 before:bg-lime-400 dark:text-white" 
                                       href="#" 
                                       aria-current="page">Incio</a>
                                </div>
                                <div>
                                    <a class="inline-block text-black hover:text-gray-600 focus:outline-none focus:text-gray-600 dark:text-white dark:hover:text-neutral-300 dark:focus:text-neutral-300" 
                                       href="#">Presentar una Prueva</a>
                                </div>
                                <div>
                                    <a class="inline-block text-black hover:text-gray-600 focus:outline-none focus:text-gray-600 dark:text-white dark:hover:text-neutral-300 dark:focus:text-neutral-300" 
                                       href="#">Sobre nosotros</a>
                                </div>
                                <div>
                                    <a class="inline-block text-black hover:text-gray-600 focus:outline-none focus:text-gray-600 dark:text-white dark:hover:text-neutral-300 dark:focus:text-neutral-300" 
                                       href="#">Blogs</a>
                                </div>
                            </div>
                        </div>
                    </nav>

                    <!-- Sign In Modal -->
                    <div id="hs-modal-signin" 
                         class="hs-overlay hidden size-full fixed top-0 start-0 z-[80] overflow-x-hidden overflow-y-auto pointer-events-auto" 
                         data-hs-overlay-keyboard="true">
                        <!-- Modal Backdrop -->
                        <div class="hs-overlay-backdrop pointer-events-auto fixed top-0 start-0 z-[80] w-full h-full bg-black/50"></div>
                        
                        <!-- Modal Content -->
                        <div class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all sm:max-w-lg sm:w-full m-3 sm:mx-auto relative z-[90] pointer-events-auto">
                            <div class="bg-white border border-gray-200 rounded-xl shadow-sm dark:bg-neutral-900 dark:border-neutral-800 relative">
                                <!-- Close Button -->
                                <div class="absolute top-4 right-4 z-[90]">
                                    <button type="button" 
                                            class="flex justify-center items-center size-7 text-sm font-semibold rounded-lg border border-transparent text-gray-800 hover:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:text-white dark:hover:bg-gray-700 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600" 
                                            data-hs-overlay="#hs-modal-signin">
                                        <span class="sr-only">Close</span>
                                        <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M18 6 6 18"></path>
                                            <path d="m6 6 12 12"></path>
                                        </svg>
                                    </button>
                                </div>

                                <div class="p-4 sm:p-7">
                                    <!-- Modal Header -->
                                    <div class="text-center">
                                        <h3 class="block text-2xl font-bold text-gray-800 dark:text-neutral-200">Sign in</h3>
                                    </div>

                                    <!-- Sign In Form -->
                                    <form method="POST" action="{{ route('login') }}" class="mt-5" id="loginForm">
                                        @csrf

                                        <!-- Mensajes de Error -->
                                        @if ($errors->any())
                                            <div class="bg-red-50 border border-red-200 text-sm text-red-600 rounded-lg p-4 mb-4 dark:bg-red-800/10 dark:border-red-900 dark:text-red-500">
                                                @if ($errors->has('email'))
                                                    <p class="mb-1">{{ $errors->first('email') }}</p>
                                                @endif
                                                @if ($errors->has('password'))
                                                    <p>{{ $errors->first('password') }}</p>
                                                @endif
                                            </div>
                                        @endif

                                        <!-- Email Field -->
                                        <div class="mb-4">
                                            <label for="email" class="block text-sm font-medium mb-2 dark:text-white">
                                                Email address
                                            </label>
                                            <input type="email" 
                                                   id="email" 
                                                   name="email" 
                                                   value="{{ old('email') }}"
                                                   class="py-3 px-4 block w-full border-2 border-black rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-800 dark:text-neutral-400 dark:focus:ring-neutral-600 @error('email') border-red-500 @enderror"
                                                   required 
                                                   autofocus>
                                        </div>

                                        <!-- Password Field -->
                                        <div class="mb-4">
                                            <label for="password" class="block text-sm font-medium mb-2 dark:text-white">
                                                Password
                                            </label>
                                            <input type="password" 
                                                   id="password" 
                                                   name="password" 
                                                   class="py-3 px-4 block w-full border-2 border-black rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-800 dark:text-neutral-400 dark:focus:ring-neutral-600 @error('password') border-red-500 @enderror"
                                                   required>
                                        </div>

                                        <!-- Remember Me Checkbox -->
                                        <div class="flex items-center mb-4">
                                            <input type="checkbox" 
                                                   id="remember" 
                                                   name="remember" 
                                                   class="shrink-0 mt-0.5 border-gray-200 rounded text-blue-600 focus:ring-blue-500 dark:bg-neutral-800 dark:border-neutral-800 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800">
                                            <label for="remember" class="text-sm text-gray-500 ms-3 dark:text-gray-400">
                                                Remember me
                                            </label>
                                        </div>

                                        <!-- Submit Button -->
                                        <button type="submit" 
                                                class="w-full py-3 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition disabled:opacity-50 disabled:pointer-events-none dark:focus:ring-offset-gray-800">
                                            Sign in
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>
        </div>
        <main class="relative overflow-hidden py-10 sm:py-16 lg:py-24">
            <div class="relative z-10 text-center">
                {{-- <div class="banner-container">
                    <img src="{{ asset('images/logo.png') }}" alt="Banner" class="banner-image rounded-lg mb-8">
                </div> --}}
                <h2 class="font-bold text-gray-800 text-3xl sm:text-4xl md:text-5xl lg:text-6xl dark:text-neutral-200 mb-4">
                    Gestor de Documentos
                    <span class="block sm:inline bg-clip-text bg-gradient-to-tl from-blue-600 to-violet-600 text-transparent animate-pulse">Quality</span>
                </h2>
            </div>
            <div class="absolute top-0 start-1/2 -z-10 size-full bg-[url('https://preline.co/assets/svg/examples/polygon-bg-element.svg')] dark:bg-[url('https://preline.co/assets/svg/examples-dark/polygon-bg-element.svg')] bg-no-repeat bg-top bg-cover transform -translate-x-1/2"></div>
        </main>

        <!-- Scripts Section -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Función para inicializar el modal
                const initializeModal = () => {
                    const modal = document.getElementById('hs-modal-signin');
                    if (!modal) return null;
                    
                    return new HSOverlay(modal, {
                        closeOnEscape: false,
                        closeOnBackdrop: false
                    });
                };
        
                // Función para abrir el modal
                const openSignInModal = () => {
                    const hsOverlay = initializeModal();
                    if (hsOverlay) {
                        hsOverlay.open();
                    }
                };
        
                // Función para mostrar errores
                const showErrors = (errors) => {
                    // Eliminar mensajes de error anteriores
                    const oldErrorContainer = document.querySelector('.bg-red-50');
                    if (oldErrorContainer) {
                        oldErrorContainer.remove();
                    }
        
                    // Crear nuevo contenedor de errores
                    if (Object.keys(errors).length > 0) {
                        const errorContainer = document.createElement('div');
                        errorContainer.className = 'bg-red-50 border border-red-200 text-sm text-red-600 rounded-lg p-4 mb-4 dark:bg-red-800/10 dark:border-red-900 dark:text-red-500';
                        
                        Object.entries(errors).forEach(([field, messages]) => {
                            const errorMessage = document.createElement('p');
                            errorMessage.className = 'mb-1';
                            errorMessage.textContent = messages[0]; // Tomamos el primer mensaje de error
                            errorContainer.appendChild(errorMessage);
        
                            // Agregar clase de error al campo correspondiente
                            const input = document.getElementById(field);
                            if (input) {
                                input.classList.add('border-red-500');
                            }
                        });
        
                        // Insertar errores antes del formulario
                        const form = document.getElementById('loginForm');
                        form.insertAdjacentElement('afterbegin', errorContainer);
                    }
                };
        
                // Manejador del formulario
                const form = document.getElementById('loginForm');
                if (form) {
                    form.addEventListener('submit', async function(e) {
                        e.preventDefault(); // Prevenir el envío tradicional del formulario
        
                        // Obtener el token CSRF
                        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                        
                        try {
                            const formData = new FormData(this);
                            const response = await fetch(this.action, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': csrfToken,
                                    'Accept': 'application/json',
                                },
                                body: formData
                            });
        
                            const data = await response.json();
        
                            if (response.ok) {
                                // Si la autenticación es exitosa, redirigir
                                window.location.href = data.redirect || '/dashboard';
                            } else {
                                // Mostrar errores de validación
                                showErrors(data.errors);
                            }
                        } catch (error) {
                            console.error('Error:', error);
                        }
                    });
        
                    // Limpiar errores al escribir
                    const inputs = form.querySelectorAll('input[type="email"], input[type="password"]');
                    inputs.forEach(input => {
                        input.addEventListener('input', function() {
                            this.classList.remove('border-red-500');
                            const errorContainer = document.querySelector('.bg-red-50');
                            if (errorContainer) {
                                const errorMessages = errorContainer.querySelectorAll('p');
                                errorMessages.forEach(message => {
                                    if (message.textContent.toLowerCase().includes(this.type)) {
                                        message.remove();
                                    }
                                });
                                if (!errorContainer.querySelectorAll('p').length) {
                                    errorContainer.remove();
                                }
                            }
                        });
                    });
                }
        
                // Si hay errores, abrir el modal
                @if($errors->any())
                    openSignInModal();
                @endif
            });
        </script>

        @stack('scripts')
    </body>
</html>