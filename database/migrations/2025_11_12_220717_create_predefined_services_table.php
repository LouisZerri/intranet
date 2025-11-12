<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('predefined_services', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nom de la prestation
            $table->text('description')->nullable(); // Description détaillée
            $table->string('category'); // Catégorie (location, etat_lieux, gestion, etc.)
            $table->decimal('default_price', 10, 2); // Prix par défaut HT
            $table->decimal('default_tva_rate', 5, 2)->default(20); // TVA par défaut
            $table->string('unit')->default('unité'); // Unité (heure, jour, forfait, m², etc.)
            $table->decimal('default_quantity', 8, 2)->default(1); // Quantité par défaut
            $table->boolean('is_active')->default(true); // Actif/Inactif
            $table->integer('sort_order')->default(0); // Ordre d'affichage
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('predefined_services');
    }
};