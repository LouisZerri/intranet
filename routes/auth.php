<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GoogleAuthController;

/**
 * Routes d'authentification
 * 
 * Routes publiques pour la connexion et routes protégées pour la déconnexion
 */

// Routes publiques (non authentifiées)
Route::middleware('guest')->group(function () {
    // Authentification classique
    Route::get('/', [AuthController::class, 'showLogin'])->name('login');
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login.form');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');

    // Authentification Google OAuth
    Route::get('/google/auth', [GoogleAuthController::class, 'redirect'])->name('google.auth');
    Route::get('/google/callback', [GoogleAuthController::class, 'callback'])->name('google.callback');
});

// Routes protégées
Route::middleware('auth')->group(function () {
    // Déconnexion
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

