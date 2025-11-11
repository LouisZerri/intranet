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
        Schema::create('quote_items', function (Blueprint $table) {
            $table->id();
            
            // Relation avec le devis
            $table->foreignId('quote_id')->constrained()->onDelete('cascade');
            
            // Champs dynamiques : quantité, prix unitaire, TVA, remise
            $table->string('description');
            $table->decimal('quantity', 10, 2)->default(1); // Quantité
            $table->decimal('unit_price', 10, 2); // Prix unitaire HT
            $table->decimal('tva_rate', 5, 2)->default(20.00); // Taux de TVA en %
            $table->decimal('total_ht', 10, 2); // Total HT calculé automatiquement
            
            // Ordre d'affichage
            $table->integer('sort_order')->default(0);
            
            $table->timestamps();
            
            // Index
            $table->index('quote_id');
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quote_items');
    }
};