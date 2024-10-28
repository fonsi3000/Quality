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
</div>
@endsection