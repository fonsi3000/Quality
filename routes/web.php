<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;

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
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});