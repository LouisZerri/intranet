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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            
            // Numérotation automatique FAC-AAAA-XXXX
            $table->string('invoice_number')->unique();
            
            // Relations
            $table->foreignId('quote_id')->nullable()->constrained()->onDelete('set null'); // Devis d'origine si converti
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Mandataire émetteur
            
            // Statuts : Brouillon / Émise / Payée / En retard / Annulée
            $table->enum('status', ['brouillon', 'emise', 'payee', 'en_retard', 'annulee'])->default('brouillon');
            
            // Montants
            $table->decimal('total_ht', 10, 2)->default(0);
            $table->decimal('total_tva', 10, 2)->default(0);
            $table->decimal('total_ttc', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->nullable();
            $table->decimal('discount_percentage', 5, 2)->nullable();
            
            // Conditions de paiement
            $table->text('payment_terms')->nullable();
            
            // Dates importantes
            $table->timestamp('issued_at')->nullable(); // Date d'émission
            $table->date('due_date')->nullable(); // Date d'échéance
            $table->timestamp('paid_at')->nullable(); // Date de paiement complet
            $table->timestamp('cancelled_at')->nullable(); // Date d'annulation
            
            // Notes internes
            $table->text('internal_notes')->nullable();
            
            // Informations de paiement
            $table->enum('payment_method', ['especes', 'cheque', 'virement', 'carte', 'prelevement'])->nullable();
            $table->string('payment_reference')->nullable(); // Référence de paiement
            
            // Rappels automatiques paramétrables
            $table->timestamp('reminder_sent_at')->nullable(); // Date du dernier rappel
            $table->integer('reminder_count')->default(0); // Nombre de rappels envoyés
            
            $table->timestamps();
            
            // Index pour améliorer les performances
            $table->index('invoice_number');
            $table->index('status');
            $table->index(['user_id', 'status']);
            $table->index('due_date');
            $table->index('issued_at');
            $table->index('paid_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};