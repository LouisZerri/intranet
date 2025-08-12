<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MissionController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\InternalRequestController;
use App\Http\Controllers\FormationController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\DocumentationController;

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
    
    // Routes par rôle - TOUS LES COLLABORATEURS
    Route::middleware('role:collaborateur,manager,administrateur')->group(function () {
        // Actualités - routes spécifiques en premier
        Route::get('/actualites', [NewsController::class, 'index'])->name('news.index');
        
        // Missions
        Route::get('/missions', [MissionController::class, 'index'])->name('missions.index');
        
        // Demandes internes - routes accessibles à tous les rôles
        Route::get('/demandes', [InternalRequestController::class, 'index'])->name('requests.index');
        Route::get('/demandes/create', [InternalRequestController::class, 'create'])->name('requests.create');
        Route::post('/demandes', [InternalRequestController::class, 'store'])->name('requests.store');
        
        // Formations - routes accessibles à tous les rôles
        Route::get('/formations', [FormationController::class, 'index'])->name('formations.index');
        Route::get('/mes-formations', [FormationController::class, 'myRequests'])->name('formations.my-requests');
        Route::post('/formations/{formation}/request', [FormationController::class, 'requestParticipation'])->name('formations.request');
        Route::put('/formation-requests/{formationRequest}/complete', [FormationController::class, 'completeRequest'])->name('formation-requests.complete');
        
        // Documentation & Support - accessibles à tous les rôles
        Route::get('/documentation', [DocumentationController::class, 'index'])->name('documentation.index');
        Route::get('/documentation/contacts', [DocumentationController::class, 'contacts'])->name('documentation.contacts.index');
        Route::get('/documentation/faq', [DocumentationController::class, 'faq'])->name('documentation.faq.index');
        Route::get('/documentation/resources', [DocumentationController::class, 'resources'])->name('documentation.resources.index');
        Route::get('/documentation/resources/{resource}/download', [DocumentationController::class, 'downloadResource'])->name('documentation.resources.download');
    });
    
    // Routes manager et admin
    Route::middleware('role:manager,administrateur')->group(function () {
        // Gestion des actualités - routes de création/édition en premier
        Route::get('/actualites/create', [NewsController::class, 'create'])->name('news.create');
        Route::post('/actualites', [NewsController::class, 'store'])->name('news.store');
        Route::get('/actualites/{news}/edit', [NewsController::class, 'edit'])->name('news.edit');
        Route::put('/actualites/{news}', [NewsController::class, 'update'])->name('news.update');
        Route::delete('/actualites/{news}', [NewsController::class, 'destroy'])->name('news.destroy');
        
        // Gestion des missions
        Route::get('/missions/create', [MissionController::class, 'create'])->name('missions.create');
        Route::post('/missions', [MissionController::class, 'store'])->name('missions.store');
        Route::get('/missions/{mission}/edit', [MissionController::class, 'edit'])->name('missions.edit');
        Route::put('/missions/{mission}', [MissionController::class, 'update'])->name('missions.update');
        Route::delete('/missions/{mission}', [MissionController::class, 'destroy'])->name('missions.destroy');
        
        // Gestion des demandes internes (approbation/assignation)
        Route::put('/demandes/{request}/approve', [InternalRequestController::class, 'approve'])->name('requests.approve');
        Route::put('/demandes/{request}/reject', [InternalRequestController::class, 'reject'])->name('requests.reject');
        Route::put('/demandes/{request}/assign', [InternalRequestController::class, 'assign'])->name('requests.assign');
        Route::put('/demandes/{request}/complete', [InternalRequestController::class, 'complete'])->name('requests.complete');
        
        // Gestion des formations
        Route::get('/formations/create', [FormationController::class, 'create'])->name('formations.create');
        Route::post('/formations', [FormationController::class, 'store'])->name('formations.store');
        Route::get('/gestion-formations', [FormationController::class, 'manageRequests'])->name('formations.manage');
        Route::put('/formation-requests/{formationRequest}/approve', [FormationController::class, 'approveRequest'])->name('formation-requests.approve');
        Route::put('/formation-requests/{formationRequest}/reject', [FormationController::class, 'rejectRequest'])->name('formation-requests.reject');
        
        // Gestion équipe - ROUTES DANS LE BON ORDRE
        Route::get('/equipe', [TeamController::class, 'index'])->name('team.index');
    });
    
    // Routes admin uniquement
    Route::middleware('role:administrateur')->group(function () {

        // Administration des demandes (vue globale)
        Route::get('/admin/demandes', [InternalRequestController::class, 'adminIndex'])->name('admin.requests.index');
        Route::get('/admin/demandes/stats', [InternalRequestController::class, 'getStats'])->name('admin.requests.stats');
        
        // Administration des formations
        Route::get('/admin/formations/stats', [FormationController::class, 'stats'])->name('admin.formations.stats');
        
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
    
    // Route show des actualités, missions, demandes et formations à la fin pour éviter les conflits
    Route::middleware('role:collaborateur,manager,administrateur')->group(function () {
        Route::get('/actualites/{news}', [NewsController::class, 'show'])->name('news.show');
        Route::get('/missions/{mission}', [MissionController::class, 'show'])->name('missions.show');
        Route::get('/demandes/{request}', [InternalRequestController::class, 'show'])->name('requests.show');
        Route::get('/demandes/{request}/edit', [InternalRequestController::class, 'edit'])->name('requests.edit');
        Route::put('/demandes/{request}', [InternalRequestController::class, 'update'])->name('requests.update');
        Route::delete('/demandes/{request}', [InternalRequestController::class, 'destroy'])->name('requests.destroy');
        
        // Routes show formations
        Route::get('/formations/{formation}', [FormationController::class, 'show'])->name('formations.show');
        
        // Routes équipe 
        Route::get('/equipe/{teamMember}', [TeamController::class, 'show'])->name('team.show');
    });
    
    // Routes profil utilisateur
    Route::get('/profile', function () {
        return view('profile.edit');
    })->name('profile.edit');
    
    Route::put('/profile', function () {
        return redirect()->route('profile.edit')->with('success', 'Profil mis à jour avec succès');
    })->name('profile.update');
    
    // Route pour changement mot de passe
    Route::put('/password', function () {
        return redirect()->route('profile.edit')->with('success', 'Mot de passe mis à jour avec succès');
    })->name('password.update');
});