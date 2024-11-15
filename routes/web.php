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

/*
|--------------------------------------------------------------------------
| Rutas Públicas
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Rutas de Autenticación
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| Rutas Protegidas por Autenticación
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // Vista general de documentos
    Route::get('/documents', [DocumentController::class, 'index'])
        ->name('documents');

    /*
    |--------------------------------------------------------------------------
    | Gestión de Documentos
    |--------------------------------------------------------------------------
    */
    Route::prefix('documents')->name('documents.')->group(function () {
        // Solicitudes de documentos - CRUD básico
        Route::resource('requests', DocumentRequestController::class);
        
        // Rutas para manejo de documentos
        Route::get('requests/{documentRequest}/download', [DocumentRequestController::class, 'downloadDocument'])
            ->name('requests.download');
        
        Route::get('requests/{documentRequest}/download-final', [DocumentRequestController::class, 'downloadFinalDocument'])
            ->name('requests.download-final');

        Route::get('requests/{documentRequest}/preview', [DocumentRequestController::class, 'previewDocument'])
            ->name('requests.preview');

        Route::get('requests/{documentRequest}/preview-final', [DocumentRequestController::class, 'previewFinalDocument'])
            ->name('requests.preview-final');

        // Rutas para gestión de estados y asignaciones
        Route::patch('requests/{documentRequest}/status', [DocumentRequestController::class, 'updateStatus'])
            ->name('requests.update-status');

        Route::put('requests/{documentRequest}/reject', [DocumentRequestController::class, 'reject'])
            ->name('requests.reject');

        Route::put('requests/{documentRequest}/assign', [DocumentRequestController::class, 'assign'])
            ->name('requests.assign');

        Route::post('requests/{documentRequest}/attach-final', [DocumentRequestController::class, 'attachFinalDocument'])
            ->name('requests.attach-final');

        // Rutas para aprobación y devolución
        Route::post('requests/{documentRequest}/approve', [DocumentRequestController::class, 'approve'])
            ->name('requests.approve');

        Route::post('requests/{documentRequest}/return-to-progress', [DocumentRequestController::class, 'returnToProgress'])
            ->name('requests.return-to-progress');

        // Vistas filtradas de documentos
        Route::get('/in-progress', [DocumentRequestController::class, 'inProgress'])
            ->name('in-progress');
        
        Route::get('/in-review', [DocumentRequestController::class, 'inReview'])
            ->name('in-review');

        // Nueva ruta para documentos publicados
        Route::get('/published', [DocumentRequestController::class, 'published'])
            ->name('published');

        // Nueva ruta para el buscador
        Route::get('/search', [DocumentRequestController::class, 'search'])
            ->name('search');
        
        // Ruta para búsqueda con AJAX
        Route::get('/search/ajax', [DocumentRequestController::class, 'search'])
            ->name('search.ajax');

        // Rutas adicionales para documentos
        Route::get('requests/{documentRequest}/preview-document', [DocumentRequestController::class, 'previewDocument'])
            ->name('requests.preview-document');
            
        Route::get('requests/{documentRequest}/download-document', [DocumentRequestController::class, 'downloadDocument'])
            ->name('requests.download-document');

        Route::get('requests/{documentRequest}/preview-final', [DocumentRequestController::class, 'previewFinalDocument'])
            ->name('requests.preview-final');
            
        Route::get('requests/{documentRequest}/download-final', [DocumentRequestController::class, 'downloadFinalDocument'])
            ->name('requests.download-final');
    });

    /*
    |--------------------------------------------------------------------------
    | Configuración de Documentos
    |--------------------------------------------------------------------------
    */
    Route::prefix('document-config')->group(function () {
        // Tipos de documentos
        Route::resource('types', DocumentTypeController::class)
            ->names('document-types');

        // Plantillas de documentos
        Route::resource('templates', DocumentTemplateController::class)
            ->names('document-templates');
        
        Route::get('templates/{template}/download', [DocumentTemplateController::class, 'download'])
            ->name('document-templates.download');
        
        Route::get('templates/{template}/preview', [DocumentTemplateController::class, 'preview'])
            ->name('document-templates.preview');
    });

    /*
    |--------------------------------------------------------------------------
    | Gestión Organizacional
    |--------------------------------------------------------------------------
    */
    Route::prefix('organization')->group(function () {
        // Usuarios
        Route::resource('users', UserController::class);
        Route::patch('/users/{user}/toggle-active', [UserController::class, 'toggleActive'])
            ->name('users.toggle-active');

        // Unidades
        Route::resource('units', UnitController::class);
        Route::patch('/units/{unit}/toggle-active', [UnitController::class, 'toggleActive'])
            ->name('units.toggle-active');

        // Cargos
        Route::resource('positions', PositionController::class);
        Route::patch('/positions/{position}/toggle-active', [PositionController::class, 'toggleActive'])
            ->name('positions.toggle-active');

        // Procesos
        Route::resource('processes', ProcessController::class);
        Route::patch('/processes/{process}/toggle-active', [ProcessController::class, 'toggleActive'])
            ->name('processes.toggle-active');
    });

    /*
    |--------------------------------------------------------------------------
    | API Routes para peticiones AJAX
    |--------------------------------------------------------------------------
    */
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/document-types', [DocumentTypeController::class, 'getActive'])
            ->name('document-types.active');
        
        Route::get('/users/active', [UserController::class, 'getActive'])
            ->name('users.active');
    });
});