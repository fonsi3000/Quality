@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="grid gap-6 mb-8 md:grid-cols-2 xl:grid-cols-4">
    <!-- Card Usuarios Activos -->
    <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-neutral-800">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <span class="flex items-center justify-center w-12 h-12 rounded-md bg-blue-50 text-blue-600 dark:bg-blue-500/20 dark:text-blue-400">
                        <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
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
                <a href="{{ route('users.index') }}" class="font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">
                    Ver todos los usuarios
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
                    <span class="flex items-center justify-center w-12 h-12 rounded-md bg-yellow-50 text-yellow-600 dark:bg-yellow-500/20 dark:text-yellow-400">
                        <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
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
                <a href="{{ route('documents.in-progress') }}" class="font-medium text-yellow-600 hover:text-yellow-500 dark:text-yellow-400 dark:hover:text-yellow-300">
                    Ver todas las tareas
                    <span aria-hidden="true"> &rarr;</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Card Documentos Publicados -->
    <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-neutral-800">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <span class="flex items-center justify-center w-12 h-12 rounded-md bg-green-50 text-green-600 dark:bg-green-500/20 dark:text-green-400">
                        <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
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
        <div class="bg-gray-50 px-5 py-3 dark:bg-neutral-800/50">
            <div class="text-sm">
                <a href="{{ route('documents.published') }}" class="font-medium text-green-600 hover:text-green-500 dark:text-green-400 dark:hover:text-green-300">
                    Ver documentos publicados
                    <span aria-hidden="true"> &rarr;</span>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection