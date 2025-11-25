<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MissionController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\CommunicationController;
use App\Http\Controllers\AdminCommunicationController;
use App\Http\Controllers\FormationController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\DocumentationController;
use App\Http\Controllers\ProfileController;

// ===== NOUVEAUX CONTROLLERS MODULE COMMERCIAL =====
use App\Http\Controllers\ClientController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\URSSAFController;

// Routes publiques (sans authentification)
Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'showLogin'])->name('login');
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login.form');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// Routes protégées (avec authentification)
Route::middleware('auth')->group(function () {
    // Déconnexion
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Tableau de bord principal
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Routes admin uniquement - PLACÉES EN PREMIER pour éviter les conflits
    Route::middleware('role:administrateur')->group(function () {
        // Gestion des formations - CRÉATION réservée aux admins (AVANT les routes génériques)
        Route::get('/formations/create', [FormationController::class, 'create'])->name('formations.create');
        Route::delete('/formations/{formation}', [FormationController::class, 'destroy'])->name('formations.destroy');
        Route::post('/formations', [FormationController::class, 'store'])->name('formations.store');

        // Gestion des fichiers de formation (admin seulement)
        Route::get('/formations/{formation}/files', [FormationController::class, 'manageFiles'])->name('formations.files.manage');
        Route::post('/formations/{formation}/files', [FormationController::class, 'uploadFiles'])->name('formations.files.upload');
        Route::delete('/formations/files/{file}', [FormationController::class, 'deleteFile'])->name('formations.files.delete');
        Route::put('/formations/files/{file}', [FormationController::class, 'updateFileMetadata'])->name('formations.files.update');
        Route::put('/formations/{formation}/files/reorder', [FormationController::class, 'reorderFiles'])->name('formations.files.reorder');

        // Administration des formations
        Route::get('/admin/formations/stats', [FormationController::class, 'stats'])->name('admin.formations.stats');

        // Gestion des produits de communication (admin seulement)
        Route::get('/admin/communication/produits', [AdminCommunicationController::class, 'products'])->name('admin.communication.products');
        Route::get('/admin/communication/produits/create', [AdminCommunicationController::class, 'createProduct'])->name('admin.communication.products.create');
        Route::post('/admin/communication/produits', [AdminCommunicationController::class, 'storeProduct'])->name('admin.communication.products.store');
        Route::get('/admin/communication/produits/{product}/edit', [AdminCommunicationController::class, 'editProduct'])->name('admin.communication.products.edit');
        Route::put('/admin/communication/produits/{product}', [AdminCommunicationController::class, 'updateProduct'])->name('admin.communication.products.update');
        Route::delete('/admin/communication/produits/{product}', [AdminCommunicationController::class, 'destroyProduct'])->name('admin.communication.products.destroy');

        // Gestion des commandes de communication (admin)
        Route::get('/admin/communication/commandes', [AdminCommunicationController::class, 'orders'])->name('admin.communication.orders');
        Route::put('/admin/communication/commandes/{order}/status', [AdminCommunicationController::class, 'updateOrderStatus'])->name('admin.communication.orders.update-status');

        // Gestion équipe avancée (admin seulement)
        Route::get('/equipe/create', [TeamController::class, 'create'])->name('team.create');
        Route::post('/equipe', [TeamController::class, 'store'])->name('team.store');
        Route::get('/equipe/{teamMember}/edit', [TeamController::class, 'edit'])->name('team.edit');
        Route::put('/equipe/{teamMember}', [TeamController::class, 'update'])->name('team.update');
        Route::put('/equipe/{teamMember}/reset-password', [TeamController::class, 'resetPassword'])->name('team.reset-password');
        Route::put('/equipe/{teamMember}/deactivate', [TeamController::class, 'deactivate'])->name('team.deactivate');
        Route::put('/equipe/{teamMember}/activate', [TeamController::class, 'activate'])->name('team.activate');
        Route::delete('/equipe/{teamMember}', [TeamController::class, 'destroy'])->name('team.destroy');

        // Gestion Documentation & Support (admin seulement)
        Route::get('/documentation/contacts/create', [DocumentationController::class, 'createContact'])->name('documentation.contacts.create');
        Route::post('/documentation/contacts', [DocumentationController::class, 'storeContact'])->name('documentation.contacts.store');
        Route::get('/documentation/contacts/{contact}/edit', [DocumentationController::class, 'editContact'])->name('documentation.contacts.edit');
        Route::put('/documentation/contacts/{contact}', [DocumentationController::class, 'updateContact'])->name('documentation.contacts.update');
        Route::delete('/documentation/contacts/{contact}', [DocumentationController::class, 'destroyContact'])->name('documentation.contacts.destroy');

        Route::get('/documentation/faq/create', [DocumentationController::class, 'createFaq'])->name('documentation.faq.create');
        Route::post('/documentation/faq', [DocumentationController::class, 'storeFaq'])->name('documentation.faq.store');
        Route::get('/documentation/faq/{faq}/edit', [DocumentationController::class, 'editFaq'])->name('documentation.faq.edit');
        Route::put('/documentation/faq/{faq}', [DocumentationController::class, 'updateFaq'])->name('documentation.faq.update');
        Route::delete('/documentation/faq/{faq}', [DocumentationController::class, 'destroyFaq'])->name('documentation.faq.destroy');

        Route::get('/documentation/resources/create', [DocumentationController::class, 'createResource'])->name('documentation.resources.create');
        Route::post('/documentation/resources', [DocumentationController::class, 'storeResource'])->name('documentation.resources.store');
        Route::get('/documentation/resources/{resource}/edit', [DocumentationController::class, 'editResource'])->name('documentation.resources.edit');
        Route::put('/documentation/resources/{resource}', [DocumentationController::class, 'updateResource'])->name('documentation.resources.update');
        Route::delete('/documentation/resources/{resource}', [DocumentationController::class, 'destroyResource'])->name('documentation.resources.destroy');
    });

    // Routes manager et admin - PLACÉES APRÈS admin pour éviter les conflits
    Route::middleware('role:manager,administrateur')->group(function () {
        // Gestion des actualités - routes spécifiques AVANT les routes génériques
        Route::get('/actualites/create', [NewsController::class, 'create'])->name('news.create');
        Route::post('/actualites', [NewsController::class, 'store'])->name('news.store');
        Route::get('/actualites/{news}/edit', [NewsController::class, 'edit'])->name('news.edit');
        Route::put('/actualites/{news}', [NewsController::class, 'update'])->name('news.update');
        Route::delete('/actualites/{news}', [NewsController::class, 'destroy'])->name('news.destroy');

        // Gestion des formations - SEULEMENT GESTION DES DEMANDES pour managers
        Route::get('/gestion-formations', [FormationController::class, 'manageRequests'])->name('formations.manage');
        Route::put('/formation-requests/{formationRequest}/approve', [FormationController::class, 'approveRequest'])->name('formation-requests.approve');
        Route::put('/formation-requests/{formationRequest}/reject', [FormationController::class, 'rejectRequest'])->name('formation-requests.reject');

        // Gestion équipe
        Route::get('/equipe', [TeamController::class, 'index'])->name('team.index');

        // MODULE COMMERCIAL - Routes managers/admin
        Route::get('/clients/top', [ClientController::class, 'topClients'])->name('clients.top');
        Route::get('/clients/overdue', [ClientController::class, 'withOverdueInvoices'])->name('clients.overdue');

        // URSSAF - Vue consolidée tous mandataires
        Route::get('/urssaf/all-mandataires', [URSSAFController::class, 'allMandataires'])->name('urssaf.all-mandataires');
        Route::post('/urssaf/all-mandataires/export-pdf', [URSSAFController::class, 'exportAllMandatairesPdf'])->name('urssaf.all-mandataires.export-pdf');
        Route::post('/urssaf/all-mandataires/export-excel', [URSSAFController::class, 'exportAllMandatairesExcel'])->name('urssaf.all-mandataires.export-excel');
    });

    // Routes par rôle - TOUS LES COLLABORATEURS
    Route::middleware('role:collaborateur,manager,administrateur')->group(function () {
        // Actualités - routes génériques APRÈS les routes spécifiques
        Route::get('/actualites', [NewsController::class, 'index'])->name('news.index');
        Route::get('/actualites/{news}', [NewsController::class, 'show'])->name('news.show');

        // Missions - Resource complète pour tous les rôles (permissions gérées dans le contrôleur)
        Route::resource('missions', MissionController::class);

        // ROUTE API: Pour récupérer les sous-catégories dynamiquement
        Route::get('/api/missions/subcategories', [MissionController::class, 'getSubcategories'])->name('missions.subcategories');

        // ============================================
        // COMMUNICATION - Remplacement des demandes internes
        // ============================================

        // Catalogue de produits de communication
        Route::get('/communication', [CommunicationController::class, 'index'])->name('communication.index');
        Route::get('/communication/produits/{product}', [CommunicationController::class, 'show'])->name('communication.show');

        // Panier
        Route::get('/communication/panier', [CommunicationController::class, 'cart'])->name('communication.cart');
        Route::post('/communication/panier/ajouter/{product}', [CommunicationController::class, 'addToCart'])->name('communication.add-to-cart');
        Route::put('/communication/panier', [CommunicationController::class, 'updateCart'])->name('communication.update-cart');
        Route::delete('/communication/panier', [CommunicationController::class, 'clearCart'])->name('communication.clear-cart');

        // Commandes
        Route::post('/communication/commander', [CommunicationController::class, 'placeOrder'])->name('communication.place-order');
        Route::get('/communication/commande/{order}/succes', [CommunicationController::class, 'orderSuccess'])->name('communication.order-success');
        Route::get('/communication/mes-commandes', [CommunicationController::class, 'myOrders'])->name('communication.my-orders');
        Route::get('/communication/commande/{order}', [CommunicationController::class, 'orderDetails'])->name('communication.order-details');

        // ============================================
        // FIN COMMUNICATION
        // ============================================

        // Formations - TOUS peuvent consulter et demander (routes spécifiques AVANT génériques)
        Route::get('/mes-formations', [FormationController::class, 'myRequests'])->name('formations.my-requests');
        Route::get('/formations', [FormationController::class, 'index'])->name('formations.index');
        Route::get('/formations/{formation}', [FormationController::class, 'show'])->name('formations.show');
        Route::post('/formations/{formation}/request', [FormationController::class, 'requestParticipation'])->name('formations.request');
        Route::put('/formation-requests/{formationRequest}/complete', [FormationController::class, 'completeRequest'])->name('formation-requests.complete');

        // Téléchargement et visualisation des fichiers (tous les utilisateurs authentifiés)
        Route::get('/formations/files/{file}/download', [FormationController::class, 'downloadFile'])->name('formations.files.download');
        Route::get('/formations/files/{file}/view', [FormationController::class, 'viewFile'])->name('formations.files.view');

        // Documentation & Support
        Route::get('/documentation', [DocumentationController::class, 'index'])->name('documentation.index');
        Route::get('/documentation/contacts', [DocumentationController::class, 'contacts'])->name('documentation.contacts.index');
        Route::get('/documentation/faq', [DocumentationController::class, 'faq'])->name('documentation.faq.index');
        Route::get('/documentation/resources', [DocumentationController::class, 'resources'])->name('documentation.resources.index');
        Route::get('/documentation/resources/{resource}/download', [DocumentationController::class, 'downloadResource'])->name('documentation.resources.download');

        // Équipe
        Route::get('/equipe/{teamMember}', [TeamController::class, 'show'])->name('team.show');

        // ============================================
        // MODULE COMMERCIAL - DEVIS, FACTURATION & URSSAF
        // ============================================

        // CLIENTS (Création rapide selon CDC)
        Route::resource('clients', ClientController::class);
        Route::get('/clients/{client}/history', [ClientController::class, 'history'])->name('clients.history');
        Route::get('/api/clients/search', [ClientController::class, 'search'])->name('clients.search'); // API autocomplete

        // DEVIS (Workflow complet CDC - Section A)
        Route::get('/quotes/predefined-services', [QuoteController::class, 'getPredefinedServices'])->name('quotes.predefined-services');
        Route::resource('quotes', QuoteController::class);
        Route::post('/quotes/{quote}/send', [QuoteController::class, 'send'])->name('quotes.send');
        Route::post('/quotes/{quote}/accept', [QuoteController::class, 'accept'])->name('quotes.accept'); // → Créer mission auto
        Route::post('/quotes/{quote}/refuse', [QuoteController::class, 'refuse'])->name('quotes.refuse');
        Route::post('/quotes/{quote}/convert', [QuoteController::class, 'convertToInvoice'])->name('quotes.convert');
        Route::get('/quotes/{quote}/pdf', [QuoteController::class, 'viewPdf'])->name('quotes.pdf');
        Route::get('/quotes/{quote}/pdf/download', [QuoteController::class, 'downloadPdf'])->name('quotes.pdf.download');


        // FACTURES (Workflow complet CDC - Section B)
        Route::resource('invoices', InvoiceController::class);

        // Actions spécifiques sur les factures
        Route::post('/invoices/{invoice}/issue', [InvoiceController::class, 'issue'])->name('invoices.issue');
        Route::post('/invoices/{invoice}/payment', [InvoiceController::class, 'recordPayment'])->name('invoices.payment');
        Route::post('/invoices/{invoice}/reminder', [InvoiceController::class, 'sendReminder'])->name('invoices.reminder');
        Route::post('/invoices/{invoice}/cancel', [InvoiceController::class, 'cancel'])->name('invoices.cancel');

        // Export PDF
        Route::get('/invoices/{invoice}/pdf', [InvoiceController::class, 'viewPdf'])->name('invoices.pdf');
        Route::get('/invoices/{invoice}/pdf/download', [InvoiceController::class, 'downloadPdf'])->name('invoices.pdf.download');

        // Historique
        Route::get('/invoices/{invoice}/history', [InvoiceController::class, 'history'])->name('invoices.history');

        // URSSAF - Récapitulatif Mandataire (CDC - Section D)
        Route::prefix('urssaf')->name('urssaf.')->group(function () {
            Route::get('/', [URSSAFController::class, 'index'])->name('index');
            Route::get('/report', [URSSAFController::class, 'report'])->name('report'); // NOUVEAU
            Route::get('/pdf', [URSSAFController::class, 'exportPdf'])->name('pdf'); // Changé en GET
            Route::get('/excel', [URSSAFController::class, 'exportExcel'])->name('excel'); // Changé en GET
        });

        // ============================================
        // FIN MODULE COMMERCIAL
        // ============================================
    });

    // Routes profil utilisateur - ACCESSIBLES À TOUS LES UTILISATEURS AUTHENTIFIÉS
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::put('/profile/professional', [ProfileController::class, 'updateProfessional'])->name('profile.update-professional');

    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.update-avatar');
    Route::delete('/profile/avatar', [ProfileController::class, 'removeAvatar'])->name('profile.remove-avatar');
    // Gestion signature
    Route::post('/profile/update-signature', [ProfileController::class, 'updateSignature'])->name('profile.update-signature');
    Route::delete('/profile/remove-signature', [ProfileController::class, 'removeSignature'])->name('profile.remove-signature');
    Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
});
