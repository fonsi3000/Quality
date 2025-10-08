<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
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
use App\Http\Controllers\SsoController;
use Illuminate\Support\Facades\Redirect;

// Ruta principal
// Route::get('/', fn() => redirect()->away('https://app.espumasmedellin-litoral.com/dashboard'));
// Ruta principal
Route::get('/', function () {
    return view('welcome');
});

Route::get('/sso-login', [SsoController::class, 'handle']);

// Route::any('/login', function (Request $request) {
//     return Redirect::away('https://app.espumasmedellin-litoral.com/dashboard');
// });
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

// Ruta de cierre de sesión
Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// Rutas protegidas que requieren autenticación
Route::middleware('auth')->group(function () {
    // Dashboard y documentos generales
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/documents', [DocumentController::class, 'index'])->name('documents');

    // Grupo de rutas para documentos
    Route::prefix('documents')->name('documents.')->group(function () {

        Route::get('requests/process-leaders', [DocumentRequestController::class, 'getProcessLeaders'])
            ->name('requests.process-leaders');
        // Resource route para las solicitudes de documentos
        Route::resource('requests', DocumentRequestController::class)->parameters([
            'requests' => 'documentRequest'
        ]);

        Route::patch('/requests/update-version/{id}', [DocumentRequestController::class, 'newVersion'])->name('update-version');
        Route::patch('/requests/obsolete/{id}',[DocumentRequestController::class, 'obsolete'])->name('obsolete');

        // Vistas principales de documentos según su estado
        Route::get('/in-progress', [DocumentRequestController::class, 'inProgress'])
            ->name('in-progress');
        Route::get('/in-review', [DocumentRequestController::class, 'inReview'])
            ->name('in-review');
        Route::get('/published', [DocumentRequestController::class, 'published'])
            ->name('published');
        Route::get('/masterdocument', [DocumentRequestController::class, 'masterdocument'])
            ->name('masterdocument');
        //ruta para obtener reporte de documentos maestros
        Route::get('/exportMasterDocuments', [DocumentRequestController::class, 'exportExcel'])->name('exportExcel');
        Route::get('/pending-leader', [DocumentRequestController::class, 'pendingLeaderApproval'])
            ->name('pending-leader');

        // Acciones de aprobación/rechazo por parte de líderes
        Route::put('requests/{documentRequest}/leader-approve', [DocumentRequestController::class, 'leaderApprove'])
            ->name('requests.leader-approve');
        Route::put('requests/{documentRequest}/leader-reject', [DocumentRequestController::class, 'leaderReject'])
            ->name('requests.leader-reject');

        // Nueva ruta para la aprobación del segundo líder
        Route::put('requests/{documentRequest}/second-leader-approve', [DocumentRequestController::class, 'secondLeaderApprove'])
            ->name('requests.second-leader-approve');

        // Rutas para manejo de documentos (descargas y previsualizaciones)
        Route::get('requests/{documentRequest}/download', [DocumentRequestController::class, 'downloadDocument'])
            ->name('requests.download');
        Route::get('requests/{documentRequest}/download-final', [DocumentRequestController::class, 'downloadFinalDocument'])
            ->name('requests.download-final');
        Route::get('requests/{documentRequest}/preview', [DocumentRequestController::class, 'previewDocument'])
            ->name('requests.preview');
        Route::get('requests/{documentRequest}/preview-final', [DocumentRequestController::class, 'previewFinalDocument'])
            ->name('requests.preview-final');

        // Gestión de estados y asignaciones de documentos
        Route::patch('requests/{documentRequest}/status', [DocumentRequestController::class, 'updateStatus'])
            ->name('requests.update-status');
        Route::put('requests/{documentRequest}/reject', [DocumentRequestController::class, 'reject'])
            ->name('requests.reject');
        Route::put('requests/{documentRequest}/assign', [DocumentRequestController::class, 'assign'])
            ->name('requests.assign');
        Route::post('requests/{documentRequest}/attach-final', [DocumentRequestController::class, 'attachFinalDocument'])
            ->name('requests.attach-final');

        // Rutas para aprobación y revisión de documentos
        Route::post('requests/{documentRequest}/approve', [DocumentRequestController::class, 'approve'])
            ->name('requests.approve');
        Route::post('requests/{documentRequest}/return-to-progress', [DocumentRequestController::class, 'returnToProgress'])
            ->name('requests.return-to-progress');

        // Nueva ruta para cambiar la visibilidad del documento
        Route::patch('requests/{documentRequest}/toggle-visibility', [DocumentRequestController::class, 'toggleVisibility'])
            ->name('requests.toggle-visibility');

        // Rutas para búsqueda y estadísticas
        Route::get('/search', [DocumentRequestController::class, 'search'])
            ->name('search');
        Route::get('/search/ajax', [DocumentRequestController::class, 'search'])
            ->name('search.ajax');
        Route::get('/statistics', [DocumentRequestController::class, 'statistics'])
            ->name('statistics');
    });

    // Rutas protegidas para administradores 
    Route::middleware('auth')->group(function () {
        Route::get('documents/requests/{documentRequest}/edit', [DocumentRequestController::class, 'edit'])
            ->name('documents.requests.edit');

        Route::delete('documents/requests/{documentRequest}', [DocumentRequestController::class, 'destroy'])
            ->name('documents.requests.destroy');
    });

    // Configuración de documentos (solo admin)
    Route::prefix('document-config')->middleware(['auth', 'can:admin.only'])->group(function () {
        Route::resource('types', DocumentTypeController::class)
            ->names('document-types');
        Route::resource('templates', DocumentTemplateController::class)
            ->names('document-templates');
        Route::get('templates/{template}/download', [DocumentTemplateController::class, 'download'])
            ->name('document-templates.download');
        Route::get('templates/{template}/preview', [DocumentTemplateController::class, 'preview'])
            ->name('document-templates.preview');
    });

    // Gestión organizacional (solo admin )
    Route::prefix('organization')->middleware(['auth', 'can:admin.only'])->group(function () {
        // Gestión de usuarios
        Route::resource('users', UserController::class);
        Route::patch('/users/{user}/toggle-active', [UserController::class, 'toggleActive'])
            ->name('users.toggle-active');

        // Gestión de unidades
        Route::resource('units', UnitController::class);
        Route::patch('/units/{unit}/toggle-active', [UnitController::class, 'toggleActive'])
            ->name('units.toggle-active');

        // Gestión de posiciones/cargos
        Route::resource('positions', PositionController::class);
        Route::patch('/positions/{position}/toggle-active', [PositionController::class, 'toggleActive'])
            ->name('positions.toggle-active');

        // Gestión de procesos
        Route::resource('processes', ProcessController::class);
        Route::patch('/processes/{process}/toggle-active', [ProcessController::class, 'toggleActive'])
            ->name('processes.toggle-active');
    });

    // Rutas API para obtener datos
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/document-types', [DocumentTypeController::class, 'getActive'])
            ->name('document-types.active');
        Route::get('/users/active', [UserController::class, 'getActive'])
            ->name('users.active');
    });

    // Gestión de líderes de proceso
    Route::put('/processes/{process}/assign-leader', [ProcessController::class, 'assignLeader'])
        ->name('processes.assign-leader');
    Route::get('/processes/{process}/leader-info', [ProcessController::class, 'getLeaderInfo'])
        ->name('processes.leader-info');
    Route::put('/processes/{process}/remove-leader', [ProcessController::class, 'removeLeader'])
        ->name('processes.remove-leader');

    // Nuevas rutas para la gestión del segundo líder
    Route::put('/processes/{process}/assign-second-leader', [ProcessController::class, 'assignSecondLeader'])
        ->name('processes.assign-second-leader');
    Route::put('/processes/{process}/remove-second-leader', [ProcessController::class, 'removeSecondLeader'])
        ->name('processes.remove-second-leader');
});
