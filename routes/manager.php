<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\FormationController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\URSSAFController;

/**
 * Routes réservées aux managers et administrateurs
 * 
 * Gestion des actualités, formations, recrutement, statistiques clients et URSSAF
 */

Route::middleware(['auth', 'role:manager,administrateur'])->group(function () {

    // ========================================================================
    // ACTUALITÉS (Gestion)
    // ========================================================================
    
    Route::prefix('actualites')->name('news.')->group(function () {
        Route::get('/create', [NewsController::class, 'create'])->name('create');
        Route::post('/', [NewsController::class, 'store'])->name('store');
        Route::get('/{news}/edit', [NewsController::class, 'edit'])->name('edit');
        Route::put('/{news}', [NewsController::class, 'update'])->name('update');
        Route::delete('/{news}', [NewsController::class, 'destroy'])->name('destroy');
    });

    // ========================================================================
    // FORMATIONS (Gestion des demandes)
    // ========================================================================
    
    Route::get('/gestion-formations', [FormationController::class, 'manageRequests'])->name('formations.manage');
    Route::prefix('formation-requests')->name('formation-requests.')->group(function () {
        Route::put('/{formationRequest}/approve', [FormationController::class, 'approveRequest'])->name('approve');
        Route::put('/{formationRequest}/reject', [FormationController::class, 'rejectRequest'])->name('reject');
    });

    // ========================================================================
    // ÉQUIPE (Consultation)
    // ========================================================================
    
    Route::get('/equipe', [TeamController::class, 'index'])->name('team.index');

    // ========================================================================
    // RECRUTEMENT
    // ========================================================================
    
    Route::prefix('equipe/recrutement')->name('recruitment.')->group(function () {
        Route::get('/', [CandidateController::class, 'index'])->name('index');
        Route::get('/create', [CandidateController::class, 'create'])->name('create');
        Route::post('/', [CandidateController::class, 'store'])->name('store');
        Route::get('/{candidate}', [CandidateController::class, 'show'])->name('show');
        Route::get('/{candidate}/edit', [CandidateController::class, 'edit'])->name('edit');
        Route::put('/{candidate}', [CandidateController::class, 'update'])->name('update');
        Route::delete('/{candidate}', [CandidateController::class, 'destroy'])->name('destroy');
        Route::patch('/{candidate}/status', [CandidateController::class, 'updateStatus'])->name('update-status');
        Route::patch('/{candidate}/ratings', [CandidateController::class, 'updateRatings'])->name('update-ratings');
    });

    // ========================================================================
    // CLIENTS (Statistiques et rapports)
    // ========================================================================
    
    Route::prefix('clients')->name('clients.')->group(function () {
        Route::get('/top', [ClientController::class, 'topClients'])->name('top');
        Route::get('/overdue', [ClientController::class, 'withOverdueInvoices'])->name('overdue');
    });

    // ========================================================================
    // URSSAF (Vue consolidée)
    // ========================================================================
    
    Route::prefix('urssaf/all-mandataires')->group(function () {
        Route::get('/', [URSSAFController::class, 'allMandataires'])->name('urssaf.all-mandataires');
        Route::post('/export-pdf', [URSSAFController::class, 'exportAllMandatairesPdf'])->name('urssaf.all-mandataires.export-pdf');
        Route::post('/export-excel', [URSSAFController::class, 'exportAllMandatairesExcel'])->name('urssaf.all-mandataires.export-excel');
    });
});

