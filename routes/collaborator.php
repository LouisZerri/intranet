<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\MissionController;
use App\Http\Controllers\CommunicationController;
use App\Http\Controllers\FormationController;
use App\Http\Controllers\DocumentationController;
use App\Http\Controllers\TeamController;

/**
 * Routes pour collaborateurs, managers et administrateurs
 * 
 * Consultation et utilisation des modules : actualités, missions, communication,
 * formations, documentation et équipe
 */

Route::middleware(['auth', 'role:collaborateur,manager,administrateur'])->group(function () {

    // ========================================================================
    // ACTUALITÉS (Consultation)
    // ========================================================================
    
    Route::prefix('actualites')->name('news.')->group(function () {
        Route::get('/', [NewsController::class, 'index'])->name('index');
        Route::get('/{news}', [NewsController::class, 'show'])->name('show');
    });

    // ========================================================================
    // MISSIONS
    // ========================================================================
    
    Route::resource('missions', MissionController::class);
    Route::get('/api/missions/subcategories', [MissionController::class, 'getSubcategories'])->name('missions.subcategories');

    // ========================================================================
    // COMMUNICATION (Catalogue et commandes)
    // ========================================================================
    
    Route::prefix('communication')->name('communication.')->group(function () {
        // Catalogue
        Route::get('/', [CommunicationController::class, 'index'])->name('index');
        Route::get('/produits/{product}', [CommunicationController::class, 'show'])->name('show');

        // Panier
        Route::get('/panier', [CommunicationController::class, 'cart'])->name('cart');
        Route::post('/panier/ajouter/{product}', [CommunicationController::class, 'addToCart'])->name('add-to-cart');
        Route::put('/panier', [CommunicationController::class, 'updateCart'])->name('update-cart');
        Route::delete('/panier', [CommunicationController::class, 'clearCart'])->name('clear-cart');

        // Commandes
        Route::post('/commander', [CommunicationController::class, 'placeOrder'])->name('place-order');
        Route::get('/commande/{order}/succes', [CommunicationController::class, 'orderSuccess'])->name('order-success');
        Route::get('/mes-commandes', [CommunicationController::class, 'myOrders'])->name('my-orders');
        Route::get('/commande/{order}', [CommunicationController::class, 'orderDetails'])->name('order-details');
    });

    // ========================================================================
    // FORMATIONS (Consultation et participation)
    // ========================================================================
    
    Route::prefix('formations')->name('formations.')->group(function () {
        Route::get('/', [FormationController::class, 'index'])->name('index');
        Route::get('/{formation}', [FormationController::class, 'show'])->name('show');
        Route::post('/{formation}/request', [FormationController::class, 'requestParticipation'])->name('request');

        // Fichiers de formation
        Route::get('/files/{file}/download', [FormationController::class, 'downloadFile'])->name('files.download');
        Route::get('/files/{file}/view', [FormationController::class, 'viewFile'])->name('files.view');
    });

    // Mes formations
    Route::get('/mes-formations', [FormationController::class, 'myRequests'])->name('formations.my-requests');
    Route::put('/formation-requests/{formationRequest}/complete', [FormationController::class, 'completeRequest'])->name('formation-requests.complete');

    // ========================================================================
    // DOCUMENTATION (Consultation)
    // ========================================================================
    
    Route::prefix('documentation')->name('documentation.')->group(function () {
        Route::get('/', [DocumentationController::class, 'index'])->name('index');
        Route::get('/contacts', [DocumentationController::class, 'contacts'])->name('contacts.index');
        Route::get('/faq', [DocumentationController::class, 'faq'])->name('faq.index');
        Route::get('/resources', [DocumentationController::class, 'resources'])->name('resources.index');
        Route::get('/resources/{resource}/download', [DocumentationController::class, 'downloadResource'])->name('resources.download');
    });

    // ========================================================================
    // ÉQUIPE (Consultation)
    // ========================================================================
    
    Route::get('/equipe/{teamMember}', [TeamController::class, 'show'])->name('team.show');
});

