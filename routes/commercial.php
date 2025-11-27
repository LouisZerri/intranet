<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\URSSAFController;

/**
 * Routes du module commercial
 * 
 * Accessibles aux collaborateurs, managers et administrateurs
 * Gestion des clients, devis, factures et URSSAF
 */

Route::middleware(['auth', 'role:collaborateur,manager,administrateur'])->group(function () {

    // ========================================================================
    // CLIENTS
    // ========================================================================
    
    Route::resource('clients', ClientController::class);
    Route::prefix('clients')->name('clients.')->group(function () {
        Route::get('/{client}/history', [ClientController::class, 'history'])->name('history');
    });
    Route::get('/api/clients/search', [ClientController::class, 'search'])->name('clients.search');

    // ========================================================================
    // DEVIS
    // ========================================================================
    
    Route::resource('quotes', QuoteController::class);
    Route::prefix('quotes')->name('quotes.')->group(function () {
        Route::get('/predefined-services', [QuoteController::class, 'getPredefinedServices'])->name('predefined-services');
        Route::post('/{quote}/send', [QuoteController::class, 'send'])->name('send');
        Route::post('/{quote}/accept', [QuoteController::class, 'accept'])->name('accept');
        Route::post('/{quote}/refuse', [QuoteController::class, 'refuse'])->name('refuse');
        Route::post('/{quote}/convert', [QuoteController::class, 'convertToInvoice'])->name('convert');
        Route::get('/{quote}/pdf', [QuoteController::class, 'viewPdf'])->name('pdf');
        Route::get('/{quote}/pdf/download', [QuoteController::class, 'downloadPdf'])->name('pdf.download');
    });

    // ========================================================================
    // FACTURES
    // ========================================================================
    
    Route::resource('invoices', InvoiceController::class);
    Route::prefix('invoices')->name('invoices.')->group(function () {
        Route::post('/{invoice}/issue', [InvoiceController::class, 'issue'])->name('issue');
        Route::post('/{invoice}/payment', [InvoiceController::class, 'recordPayment'])->name('payment');
        Route::post('/{invoice}/reminder', [InvoiceController::class, 'sendReminder'])->name('reminder');
        Route::post('/{invoice}/cancel', [InvoiceController::class, 'cancel'])->name('cancel');
        Route::get('/{invoice}/pdf', [InvoiceController::class, 'viewPdf'])->name('pdf');
        Route::get('/{invoice}/pdf/download', [InvoiceController::class, 'downloadPdf'])->name('pdf.download');
        Route::get('/{invoice}/history', [InvoiceController::class, 'history'])->name('history');
    });

    // ========================================================================
    // URSSAF
    // ========================================================================
    
    Route::prefix('urssaf')->name('urssaf.')->group(function () {
        Route::get('/', [URSSAFController::class, 'index'])->name('index');
        Route::get('/report', [URSSAFController::class, 'report'])->name('report');
        Route::get('/pdf', [URSSAFController::class, 'exportPdf'])->name('pdf');
        Route::get('/excel', [URSSAFController::class, 'exportExcel'])->name('excel');
    });
});

