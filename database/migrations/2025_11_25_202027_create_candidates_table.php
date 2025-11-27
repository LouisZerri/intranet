<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('candidates', function (Blueprint $table) {
            $table->id();
            
            // Informations personnelles
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('city')->nullable();
            $table->string('department')->nullable();
            
            // Poste visé
            $table->string('position_applied')->nullable();
            $table->string('desired_location')->nullable();
            $table->date('available_from')->nullable();
            
            // CV et documents
            $table->string('cv_path')->nullable();
            $table->string('cover_letter_path')->nullable();
             // Dossier Google Drive du candidat
            $table->string('google_drive_folder_id')->nullable();
            
            // Liens Google Drive pour chaque document
            $table->string('cv_drive_link')->nullable();
            $table->string('cover_letter_drive_link')->nullable();
            
            // Nouveaux documents
            $table->string('identity_card_path')->nullable();
            $table->string('identity_card_drive_link')->nullable();
            
            $table->string('proof_of_address_path')->nullable();
            $table->string('proof_of_address_drive_link')->nullable();
            
            $table->string('legal_status_path')->nullable();
            $table->string('legal_status_drive_link')->nullable();
            
            $table->string('rcp_insurance_path')->nullable();
            $table->string('rcp_insurance_drive_link')->nullable();
            
            $table->string('signed_contract_path')->nullable();
            $table->string('signed_contract_drive_link')->nullable();
            
            $table->string('criminal_record_path')->nullable();
            $table->string('criminal_record_drive_link')->nullable();
            
            $table->string('rib_path')->nullable();
            $table->string('rib_drive_link')->nullable();

            $table->string('training_certificate_path')->nullable();
            $table->string('training_certificate_drive_link')->nullable();
            
            // Notations (1 à 5 étoiles)
            $table->tinyInteger('rating_motivation')->nullable();
            $table->tinyInteger('rating_seriousness')->nullable();
            $table->tinyInteger('rating_experience')->nullable();
            $table->tinyInteger('rating_commercial_skills')->nullable();
            
            // Notes et commentaires
            $table->text('notes')->nullable();
            $table->text('interview_notes')->nullable();
            
            // Statut du recrutement
            $table->enum('status', ['new', 'in_review', 'interview', 'recruited', 'integrated', 'refused'])->default('new');
            
            // Source de la candidature
            $table->string('source')->nullable();
            
            // Suivi
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('converted_to_user_id')->nullable()->constrained('users')->nullOnDelete();
            
            // Dates importantes
            $table->date('interview_date')->nullable();
            $table->date('decision_date')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('candidates');
    }
};