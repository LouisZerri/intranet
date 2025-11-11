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
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            
            // Relation avec la facture
            $table->foreignId('invoice_id')->constrained()->onDelete('cascade');
            
            // Détails de la ligne
            $table->string('description');
            $table->decimal('quantity', 10, 2)->default(1);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('tva_rate', 5, 2)->default(20.00);
            $table->decimal('total_ht', 10, 2);
            
            // Ordre d'affichage
            $table->integer('sort_order')->default(0);
            
            $table->timestamps();
            
            // Index
            $table->index('invoice_id');
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};