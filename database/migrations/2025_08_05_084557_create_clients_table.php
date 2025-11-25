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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            
            // Type de client
            $table->enum('type', ['particulier', 'professionnel'])->default('particulier');
            
            // Informations de base
            $table->string('name'); // Nom du contact ou raison sociale
            $table->string('company_name')->nullable(); // Nom de l'entreprise (si professionnel)
            $table->string('siret')->nullable(); // SIRET (si professionnel)
            $table->string('tva_number')->nullable(); // Numéro de TVA intracommunautaire
            
            // Contact
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('mobile')->nullable();
            
            // Adresse
            $table->text('address')->nullable();
            $table->string('postal_code', 10)->nullable();
            $table->string('city')->nullable();
            $table->string('country')->default('France');
            
            // Notes internes
            $table->text('notes')->nullable();
            
            // Statut
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            
            // Index pour améliorer les performances
            $table->index('type');
            $table->index('is_active');
            $table->index('email');
            $table->index('siret');
            $table->index('user_id');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};