<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invoice_payments', function (Blueprint $table) {
            $table->id();
            
            // Relation avec la facture
            $table->foreignId('invoice_id')->constrained()->onDelete('cascade');
            
            // Montant du paiement (permet les paiements partiels)
            $table->decimal('amount', 10, 2);
            
            // Méthode de paiement
            $table->enum('payment_method', ['especes', 'cheque', 'virement', 'carte', 'prelevement'])->default('virement');
            
            // Référence du paiement
            $table->string('payment_reference')->nullable();
            
            // Date du paiement
            $table->timestamp('payment_date');
            
            // Notes optionnelles
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            // Index
            $table->index('invoice_id');
            $table->index('payment_date');
            $table->index('payment_method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_payments');
    }
};