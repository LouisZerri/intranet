<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;

/**
 * Routes de gestion du profil utilisateur
 * 
 * Accessibles à tous les utilisateurs authentifiés
 */

Route::middleware('auth')->group(function () {
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::put('/', [ProfileController::class, 'update'])->name('update');
        Route::put('/professional', [ProfileController::class, 'updateProfessional'])->name('update-professional');
        
        // Avatar
        Route::post('/avatar', [ProfileController::class, 'updateAvatar'])->name('update-avatar');
        Route::delete('/avatar', [ProfileController::class, 'removeAvatar'])->name('remove-avatar');
        
        // Signature
        Route::post('/update-signature', [ProfileController::class, 'updateSignature'])->name('update-signature');
        Route::delete('/remove-signature', [ProfileController::class, 'removeSignature'])->name('remove-signature');
    });

    // Mot de passe
    Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
});

