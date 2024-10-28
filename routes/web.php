<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\ProcessController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserController; // Se añade el controlador User
use App\Http\Controllers\DashboardController;

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

    // CRUD de Usuarios
    Route::resource('users', UserController::class);
    // Ruta para toggle active
    Route::patch('/users/{user}/toggle-active', [UserController::class, 'toggleActive'])
        ->name('users.toggle-active');

    Route::resource('units', UnitController::class);
    Route::resource('positions', PositionController::class);
    Route::resource('processes', ProcessController::class);
});