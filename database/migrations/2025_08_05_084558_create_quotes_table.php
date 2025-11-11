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
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            
            // Numérotation automatique DV-AAAA-XXXX
            $table->string('quote_number')->unique();
            
            // Relations
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Mandataire émetteur
            
            // Type de prestation (sélection paramétrée)
            $table->enum('service', [
                'location',
                'etat_lieux_entree',
                'etat_lieux_sortie',
                'gestion',
                'syndic',
                'transaction',
                'expertise',
                'consultation',
                'autres'
            ])->nullable();
            
            // Statuts : Brouillon → Envoyé → Accepté → Refusé → Converti
            $table->enum('status', ['brouillon', 'envoye', 'accepte', 'refuse', 'converti'])->default('brouillon');
            
            // Montants
            $table->decimal('total_ht', 10, 2)->default(0);
            $table->decimal('total_tva', 10, 2)->default(0);
            $table->decimal('total_ttc', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->nullable(); // Remise en euros
            $table->decimal('discount_percentage', 5, 2)->nullable(); // Remise en %
            
            // Dates importantes
            $table->date('validity_date')->nullable(); // Date de validité du devis
             $table->timestamp('sent_at')->nullable();
            $table->timestamp('accepted_at')->nullable(); // Date d'acceptation
            $table->timestamp('refused_at')->nullable(); // Date de refus
            $table->timestamp('converted_at')->nullable(); // Date de conversion en facture
            
            // Notes
            $table->text('internal_notes')->nullable(); // Commentaires internes
            $table->text('client_notes')->nullable(); // Notes visibles par le client
            
            // Conditions
            $table->text('payment_terms')->nullable(); // Conditions de paiement
            $table->text('delivery_terms')->nullable(); // Conditions de livraison
            
            // Signature électronique (optionnelle)
            $table->boolean('signed_electronically')->default(false);
            $table->timestamp('signature_date')->nullable();
            
            $table->timestamps();
            
            // Index pour améliorer les performances
            $table->index('quote_number');
            $table->index('status');
            $table->index(['user_id', 'status']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotes');
    }
};