<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FormationController;
use App\Http\Controllers\AdminCommunicationController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\DocumentationController;

/**
 * Routes réservées aux administrateurs
 * 
 * Gestion complète des formations, communication, équipe et documentation
 */

Route::middleware(['auth', 'role:administrateur'])->group(function () {

    // ========================================================================
    // FORMATIONS (Gestion complète)
    // ========================================================================
    
    Route::prefix('formations')->name('formations.')->group(function () {
        // CRUD formations
        Route::get('/create', [FormationController::class, 'create'])->name('create');
        Route::post('/', [FormationController::class, 'store'])->name('store');
        Route::delete('/{formation}', [FormationController::class, 'destroy'])->name('destroy');

        // Gestion des fichiers de formation
        Route::get('/{formation}/files', [FormationController::class, 'manageFiles'])->name('files.manage');
        Route::post('/{formation}/files', [FormationController::class, 'uploadFiles'])->name('files.upload');
        Route::put('/{formation}/files/reorder', [FormationController::class, 'reorderFiles'])->name('files.reorder');
        Route::put('/files/{file}', [FormationController::class, 'updateFileMetadata'])->name('files.update');
        Route::delete('/files/{file}', [FormationController::class, 'deleteFile'])->name('files.delete');
    });

    // Statistiques formations
    Route::get('/admin/formations/stats', [FormationController::class, 'stats'])->name('admin.formations.stats');

    // ========================================================================
    // COMMUNICATION (Administration produits et commandes)
    // ========================================================================
    
    Route::prefix('admin/communication')->name('admin.communication.')->group(function () {
        // Gestion des produits
        Route::prefix('produits')->name('products.')->group(function () {
            Route::get('/', [AdminCommunicationController::class, 'products'])->name('index');
            Route::get('/create', [AdminCommunicationController::class, 'createProduct'])->name('create');
            Route::post('/', [AdminCommunicationController::class, 'storeProduct'])->name('store');
            Route::get('/{product}/edit', [AdminCommunicationController::class, 'editProduct'])->name('edit');
            Route::put('/{product}', [AdminCommunicationController::class, 'updateProduct'])->name('update');
            Route::delete('/{product}', [AdminCommunicationController::class, 'destroyProduct'])->name('destroy');
        });

        // Gestion des commandes
        Route::prefix('commandes')->name('orders.')->group(function () {
            Route::get('/', [AdminCommunicationController::class, 'orders'])->name('index');
            Route::put('/{order}/status', [AdminCommunicationController::class, 'updateOrderStatus'])->name('update-status');
        });
    });

    // ========================================================================
    // GESTION DE L'ÉQUIPE (CRUD complet)
    // ========================================================================
    
    Route::prefix('equipe')->name('team.')->group(function () {
        Route::get('/create', [TeamController::class, 'create'])->name('create');
        Route::post('/', [TeamController::class, 'store'])->name('store');
        Route::get('/{teamMember}/edit', [TeamController::class, 'edit'])->name('edit');
        Route::put('/{teamMember}', [TeamController::class, 'update'])->name('update');
        Route::delete('/{teamMember}', [TeamController::class, 'destroy'])->name('destroy');

        // Actions sur les membres
        Route::put('/{teamMember}/reset-password', [TeamController::class, 'resetPassword'])->name('reset-password');
        Route::put('/{teamMember}/deactivate', [TeamController::class, 'deactivate'])->name('deactivate');
        Route::put('/{teamMember}/activate', [TeamController::class, 'activate'])->name('activate');
    });

    // ========================================================================
    // DOCUMENTATION ET SUPPORT (CRUD)
    // ========================================================================
    
    Route::prefix('documentation')->name('documentation.')->group(function () {
        // Contacts
        Route::prefix('contacts')->name('contacts.')->group(function () {
            Route::get('/create', [DocumentationController::class, 'createContact'])->name('create');
            Route::post('/', [DocumentationController::class, 'storeContact'])->name('store');
            Route::get('/{contact}/edit', [DocumentationController::class, 'editContact'])->name('edit');
            Route::put('/{contact}', [DocumentationController::class, 'updateContact'])->name('update');
            Route::delete('/{contact}', [DocumentationController::class, 'destroyContact'])->name('destroy');
        });

        // FAQ
        Route::prefix('faq')->name('faq.')->group(function () {
            Route::get('/create', [DocumentationController::class, 'createFaq'])->name('create');
            Route::post('/', [DocumentationController::class, 'storeFaq'])->name('store');
            Route::get('/{faq}/edit', [DocumentationController::class, 'editFaq'])->name('edit');
            Route::put('/{faq}', [DocumentationController::class, 'updateFaq'])->name('update');
            Route::delete('/{faq}', [DocumentationController::class, 'destroyFaq'])->name('destroy');
        });

        // Ressources
        Route::prefix('resources')->name('resources.')->group(function () {
            Route::get('/create', [DocumentationController::class, 'createResource'])->name('create');
            Route::post('/', [DocumentationController::class, 'storeResource'])->name('store');
            Route::get('/{resource}/edit', [DocumentationController::class, 'editResource'])->name('edit');
            Route::put('/{resource}', [DocumentationController::class, 'updateResource'])->name('update');
            Route::delete('/{resource}', [DocumentationController::class, 'destroyResource'])->name('destroy');
        });
    });
});

