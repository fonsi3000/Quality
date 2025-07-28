@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="grid gap-6 mb-8 md:grid-cols-2 xl:grid-cols-4">
        <!-- Card Usuarios Activos -->
        <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-neutral-800">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <span
                            class="flex items-center justify-center w-12 h-12 rounded-md bg-blue-50 text-blue-600 dark:bg-blue-500/20 dark:text-blue-400">
                            <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </span>
                    </div>
                    <div class="ms-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate dark:text-neutral-400">
                                Usuarios Activos
                            </dt>
                            <dd>
                                <div class="text-lg font-bold text-gray-900 dark:text-neutral-200">
                                    {{ $activeUsersCount ?? 0 }}
                                </div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-5 py-3 dark:bg-neutral-800/50">
                <div class="text-sm">
                    <a href="{{ route('users.index') }}"
                        class="font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">
                        Ver todos los usuarios
                        <span aria-hidden="true"> &rarr;</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Card Usuarios Inactivos -->
        <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-neutral-800">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <span
                            class="flex items-center justify-center w-12 h-12 rounded-md bg-blue-50 text-blue-600 dark:bg-blue-500/20 dark:text-blue-400">
                            <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </span>
                    </div>
                    <div class="ms-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate dark:text-neutral-400">
                                Usuarios Inactivos
                            </dt>
                            <dd>
                                <div class="text-lg font-bold text-gray-900 dark:text-neutral-200">
                                    {{ $inactiveUsersCount ?? 0 }}
                                </div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-5 py-3 dark:bg-neutral-800/50">
                <div class="text-sm">
                    <a href="{{ route('users.index') }}"
                        class="font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">
                        Ver todos los usuarios Inactivos
                        <span aria-hidden="true"> &rarr;</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Card Tareas Activas -->
        <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-neutral-800">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <span
                            class="flex items-center justify-center w-12 h-12 rounded-md bg-yellow-50 text-yellow-600 dark:bg-yellow-500/20 dark:text-yellow-400">
                            <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </span>
                    </div>
                    <div class="ms-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate dark:text-neutral-400">
                                Tareas Activas
                            </dt>
                            <dd>
                                <div class="text-lg font-bold text-gray-900 dark:text-neutral-200">
                                    {{ $activeTasks ?? 0 }}
                                </div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-5 py-3 dark:bg-neutral-800/50">
                <div class="text-sm">
                    <a href="#"
                        class="font-medium text-yellow-600 hover:text-yellow-500 dark:text-yellow-400 dark:hover:text-yellow-300">
                        Documentos sin gestionar
                        {{-- <span aria-hidden="true"> &rarr;</span> --}}
                    </a>
                </div>
            </div>
        </div>

        <!-- Card Documentos Publicados -->
        <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-neutral-800">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <span
                            class="flex items-center justify-center w-12 h-12 rounded-md bg-green-50 text-green-600 dark:bg-green-500/20 dark:text-green-400">
                            <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </span>
                    </div>
                    <div class="ms-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate dark:text-neutral-400">
                                Documentos Publicados
                            </dt>
                            <dd>
                                <div class="text-lg font-bold text-gray-900 dark:text-neutral-200">
                                    {{ $publishedDocuments ?? 0 }}
                                </div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-6 text-center">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-neutral-200">Procesos</h2>
    </div>

    <!--Procesos principales-->
    <div class="grid gap-6 mb-8 md:grid-cols-2 xl:grid-cols-4">
        @foreach ($mainProcess as $ps)
            <div class="proc-card overflow-hidden rounded-lg bg-white shadow dark:bg-neutral-800 cursor-pointer hover:shadow-lg transform hover:scale-105 transition-all duration-200"
                data-process-name="{{ $ps->name }}" process-docs='@json($ps->documentRequests)'>
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span
                                class="flex items-center justify-center w-12 h-12 rounded-md bg-purple-50 text-purple-600 dark:bg-purple-500/20 dark:text-purple-400">
                                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </span>
                        </div>
                        <div class="ms-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate dark:text-neutral-400">
                                    {{ $ps->name }}
                                </dt>
                                <dd>
                                    <div class="text-lg font-bold text-gray-900 dark:text-neutral-200">
                                        {{ count($ps->documentRequests) }}
                                    </div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!--Otros procesos-->
    <div id="otros" class="grid gap-6 mb-8 md:grid-cols-2 xl:grid-cols-4">
        @foreach ($otherProcess as $ps)
            <div class="proc-card overflow-hidden rounded-lg bg-white shadow dark:bg-neutral-800 cursor-pointer hover:shadow-lg transform hover:scale-105 transition-all duration-200"
                data-process-name="{{ $ps->name }}" process-docs="@json($ps->documentRequests)">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span
                                class="flex items-center justify-center w-12 h-12 rounded-md bg-purple-50 text-purple-600 dark:bg-purple-500/20 dark:text-purple-400">
                                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </span>
                        </div>
                        <div class="ms-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate dark:text-neutral-400">
                                    {{ $ps->name }}
                                </dt>
                                <dd>
                                    <div class="text-lg font-bold text-gray-900 dark:text-neutral-200">
                                        {{ count($ps->documentRequests) }}
                                    </div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3 dark:bg-neutral-800/50">
                    <div class="text-sm">
                        <a href="{{ route('documents.published') }}"
                            class="font-medium text-purple-600 hover:text-purple-500 dark:text-purple-400 dark:hover:text-purple-300">
                            Ver documentos publicados
                            <span aria-hidden="true"> &rarr;</span>
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Botón Ver más -->
    @if (count($otherProcess) > 0)
        <div class="flex justify-center mb-8">
            <button id="verMasBtn" type="button"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 dark:bg-purple-500 dark:hover:bg-purple-600 dark:focus:ring-purple-400 transition-colors duration-200">
                Ver más
                <svg class="ml-2 -mr-1 w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
        </div>
    @endif

    <!-- Mensaje cuando no hay procesos -->
    @if (empty($mainProcess) && empty($otherProcess))
        <div class="col-span-full">
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-neutral-200">No hay procesos</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-neutral-400">Las procesos apareceran cuando se creen nuevos.</p>
            </div>
        </div>
    @endif

    <!-- Modal Overlay -->
    <div id="modal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-black bg-opacity-50 backdrop-blur-sm transition-opacity duration-300">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="relative w-full max-w-4xl mx-auto bg-white dark:bg-neutral-900 rounded-xl shadow-2xl transform transition-all duration-300 scale-95 opacity-0" id="modal-content">
                <!-- Header del Modal -->
                <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-neutral-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-neutral-200" id="modal-title">
                        Documentos del Proceso
                    </h3>
                    <button id="modal-close" 
                        class="inline-flex items-center justify-center w-8 h-8 text-gray-400 hover:text-gray-600 dark:text-neutral-500 dark:hover:text-neutral-300 rounded-full hover:bg-gray-100 dark:hover:bg-neutral-800 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 dark:focus:ring-offset-neutral-900">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Contenido del Modal -->
                <div class="max-h-96 overflow-y-auto">
                    <div class="overflow-x-auto">
                        <table id="modal-table" class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                            <thead class="bg-gray-50 dark:bg-neutral-800 sticky top-0 z-10">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left">
                                        <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 dark:text-neutral-200">
                                            Nombre del Documento
                                        </span>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left">
                                        <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 dark:text-neutral-200">
                                            Versión
                                        </span>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left">
                                        <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 dark:text-neutral-200">
                                            Fecha de actualización
                                        </span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="modal-body" class="divide-y divide-gray-200 dark:divide-neutral-700 bg-white dark:bg-neutral-900">
                                <!-- Contenido dinámico -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Mensaje cuando no hay documentos -->
                <div id="modal-empty" class="hidden text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-neutral-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-neutral-200">No hay documentos</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-neutral-400">Este proceso no tiene documentos asociados.</p>
                </div>

                <!-- Footer del Modal -->
                <div class="flex justify-end p-6 border-t border-gray-200 dark:border-neutral-700">
                    <button id="modal-close-2" 
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-neutral-300 bg-white dark:bg-neutral-800 border border-gray-300 dark:border-neutral-600 rounded-md hover:bg-gray-50 dark:hover:bg-neutral-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 dark:focus:ring-offset-neutral-900 transition-colors duration-200">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.proc-card');
            const modal = document.getElementById('modal');
            const modalContent = document.getElementById('modal-content');
            const modalTitle = document.getElementById('modal-title');
            const table = document.getElementById('modal-table');
            const tbody = document.getElementById('modal-body');
            const emptyMsg = document.getElementById('modal-empty');
            const btnClose = document.getElementById('modal-close');
            const btnClose2 = document.getElementById('modal-close-2');

            // Función para abrir modal con datos
            function openModal(procName, docs) {
                tbody.innerHTML = '';
                modalTitle.textContent = `Documentos del Proceso: ${procName}`;

                if (docs.length > 0) {
                    docs.forEach((d, i) => {
                        const tr = document.createElement('tr');
                        tr.classList.add('hover:bg-gray-50', 'dark:hover:bg-neutral-800/50', 'transition-colors', 'duration-150');
                        tr.innerHTML = `
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="text-sm font-medium text-gray-900 dark:text-neutral-200">
                                        ${d.document_name}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400">
                                    ${d.version}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-500 dark:text-neutral-400">
                                    ${new Date(d.updated_at).toLocaleDateString()}
                                </span>
                            </td>
                        `;
                        tbody.appendChild(tr);
                    });
                    table.classList.remove('hidden');
                    emptyMsg.classList.add('hidden');
                } else {
                    table.classList.add('hidden');
                    emptyMsg.classList.remove('hidden');
                }

                // Mostrar modal con animación
                modal.classList.remove('hidden');
                setTimeout(() => {
                    modal.classList.add('opacity-100');
                    modalContent.classList.remove('scale-95', 'opacity-0');
                    modalContent.classList.add('scale-100', 'opacity-100');
                }, 10);
            }

            // Cerrar modal
            function closeModal() {
                modalContent.classList.remove('scale-100', 'opacity-100');
                modalContent.classList.add('scale-95', 'opacity-0');
                modal.classList.remove('opacity-100');
                
                setTimeout(() => {
                    modal.classList.add('hidden');
                }, 300);
            }

            // Asignar evento clic a cada tarjeta
            cards.forEach(card => {
                card.addEventListener('click', () => {
                    const procName = card.getAttribute('data-process-name');
                    const raw = card.getAttribute('process-docs');
                    const docs = JSON.parse(raw);
                    openModal(procName, docs);
                });
            });

            // Eventos para cerrar
            btnClose.addEventListener('click', closeModal);
            btnClose2.addEventListener('click', closeModal);
            
            // Cerrar al hacer clic en el overlay
            modal.addEventListener('click', (e) => {
                if (e.target === modal) closeModal();
            });

            // Cerrar con tecla Escape
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                    closeModal();
                }
            });
        });

        document.getElementById('verMasBtn').addEventListener('click', function() {
            const cont = document.getElementById('otros');
            const btn = this;
            cont.classList.toggle('hidden');
            btn.textContent = cont.classList.contains('hidden') ? 'Ver más' : 'Ver menos';
        });
    </script>

@endsection