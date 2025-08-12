<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('internal_requests', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['achat_produit_communication', 'documentation_manager', 'prestation']);
            $table->string('title');
            $table->text('description');
            $table->text('comments')->nullable();
            
            // Pour les prestations
            $table->enum('prestation_type', ['location', 'syndic', 'menage', 'travaux', 'autres_administratifs'])->nullable();
            
            // Statut et workflow
            $table->enum('status', ['en_attente', 'valide', 'rejete', 'en_cours', 'termine'])->default('en_attente');
            $table->text('rejection_reason')->nullable();
            
            // Relations
            $table->unsignedBigInteger('requested_by');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->unsignedBigInteger('assigned_to')->nullable();
            
            // Dates
            $table->datetime('requested_at');
            $table->datetime('approved_at')->nullable();
            $table->datetime('completed_at')->nullable();
            
            // Métadonnées
            $table->json('attachments')->nullable();
            $table->decimal('estimated_cost', 10, 2)->nullable();
            $table->timestamps();
            
            $table->foreign('requested_by')->references('id')->on('users');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('set null');
            $table->index(['requested_by', 'status']);
            $table->index(['type', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('internal_requests');
    }
};