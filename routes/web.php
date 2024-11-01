<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DocumentRequestController;
use App\Http\Controllers\DocumentTemplateController;
use App\Http\Controllers\DocumentTypeController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\ProcessController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserController;

// Rutas públicas
Route::get('/', function () {
    return view('welcome');
});

// Ruta de login
Route::post('/login', [LoginController::class, 'login'])->name('login');

// Rutas protegidas
Route::middleware('auth')->group(function () {
    // Ruta de logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // Documentos (ruta simple)
    Route::get('/documents', [DocumentController::class, 'index'])->name('documents');

    // Gestión de Documentos
    Route::prefix('documents')->name('documents.')->group(function () {
        // Solicitud de documento
        Route::resource('requests', DocumentRequestController::class)
            ->names('document-requests');

        // Documentos en elaboración
        Route::get('/in-progress', [DocumentController::class, 'inProgress'])
            ->name('in-progress');

        // Documentos en revisión
        Route::get('/in-review', [DocumentController::class, 'inReview'])
            ->name('in-review');
    });

    // Configuración de Documentos
    Route::prefix('document-config')->group(function () {
        // Tipos de documentos
        Route::resource('types', DocumentTypeController::class)
            ->names('document-types');

        // Plantillas de documento
        Route::resource('templates', DocumentTemplateController::class)
            ->names('document-templates');
        
        // Agregar esta nueva ruta para la descarga
        Route::get('templates/{template}/download', [DocumentTemplateController::class, 'download'])
        ->name('document-templates.download');
        
        Route::get('document-templates/{template}/preview', [DocumentTemplateController::class, 'preview'])
        ->name('document-templates.preview');
    });

    // Organización
    Route::prefix('organization')->group(function () {
        // CRUD de Usuarios
        Route::resource('users', UserController::class);
        Route::patch('/users/{user}/toggle-active', [UserController::class, 'toggleActive'])
            ->name('users.toggle-active');

        // Unidades
        Route::resource('units', UnitController::class);

        // Cargos
        Route::resource('positions', PositionController::class);

        // Procesos
        Route::resource('processes', ProcessController::class);
    });
});